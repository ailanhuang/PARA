<!--------------------------------------------------
            E-mail: xbzhang@simm.ac.cn
            Created: 2015-5-20
            Last Modify: 2015-5-20
            Version 0.01
-------------2015------Xinben-Zhang--------------->

<!-----

---->

<?php
echo "Welcome to ML Daemon \n";
date_default_timezone_set("Asia/Shanghai");
// Import the configuration file
//include 'db.inc.php';

// Connect to the database
define("RunningFolderPath", "/home/finalML/work.temp/");
define("DownloadFolderPath", "/finalML/finalML/download/");
define("PaDEL_Descriptor","/home/gmwang/alhuang/softwarepath/PaDEL-Descriptor");
define("ML_Path","/home/gmwang/alhuang/commandline/");

require 'Client.php';
$redis = new Credis_Client('localhost');
$redis->setReadTimeout(-1);

$job_running_error = $redis->sdiff('running','finished','error');
$job_submited=$redis->lrange('submited','0','-1');
$job_restart = array_diff($job_running_error,$job_submited);
for($i=0;$i<count($job_restart);$i++){
    if(!strlen($job_restart[$i])>0)
        break;
    echo $job_restart[$i]." restart\n";
    $redis->rpush('submited',$job_restart[$i]);
    $redis->srem('running',$job_restart[$i]);
    $redis->del($job_restart[$i]."_finishedtime");
    $redis->del($job_restart[$i]."_begintime");
}

do{
    $jobs_waiting = $redis->llen('submited');
	while($jobs_waiting>0){
		echo "jobs waiting:" . $jobs_waiting."\n";
		$jobtitle = $redis->rpop('submited');
		$redis->sadd('running',$jobtitle);
		echo "Get job: ".$jobtitle."\n";
		echo "Current Time: " . date(DATE_RFC822) . "\n";
		echo "begin to run job: ".$jobtitle."\n" ;
		$redis->set($jobtitle.'_begintime',date("Y-m-d/H:i:s"));
		
		$task_running_full_path = RunningFolderPath.$jobtitle;
		echo "Check 0: Temp Running Folder: " . $task_running_full_path ."\n";
		system ('mkdir '.$task_running_full_path);
		
		if(file_exists($task_running_full_path)){ //是否有文件夹存在
			if(is_dir($task_running_full_path)){
				//check1: 文件夹是否成功创建？
				echo "Check 1: Temp Running Folder " . $task_running_full_path . " created. \n" ;
				chdir($task_running_full_path);
				//输入文件
				$string_input1_filename = $redis->get($jobtitle);
				$string_input_file_full_path = $task_running_full_path . '/' . $string_input1_filename;
				file_put_contents($string_input_file_full_path, $redis->get($jobtitle.'_moleculefile'));
				echo "Check 2: file output successfully,  as ". $string_input_file_full_path ."\n";
				system("chmod -R 777 " . $task_running_full_path);

				while(1){
					$string_PaDEL_outputfilename=$task_running_full_path . '/PaDEL_output.csv';
					$string_PaDEL_command='unset DISPLAY;java -jar '.PaDEL_Descriptor.'/PaDEL-Descriptor.jar -2d -removesalt -detectaromaticity -standardizenitro -dir '.$string_input_file_full_path.' -file '.$string_PaDEL_outputfilename;
					system($string_PaDEL_command);
					if(!file_exists($string_PaDEL_outputfilename)){
						echo "PaDEL-Descriptor is error.\n";
						$redis->set($jobtitle.'_ml_error',"1");
						$redis->append($jobtitle.'_errormessage',nl2br("PaDEL-Descriptor is error."));
                                                $redis->sadd($jobtitle);
						break;
					}
				
					$string_extractPropeties_outputfilename=$task_running_full_path.'/extractPropeties.csv';
					$string_extractPropeties_command='extractPropeties.pl '.$string_PaDEL_outputfilename.' > '.$string_extractPropeties_outputfilename;
					system($string_extractPropeties_command);
					if(!file_exists($string_extractPropeties_outputfilename)){
						echo "extractPropeties is error.\n";
						$redis->set($jobtitle.'_ml_error',"1");
						$redis->append($jobtitle.'_errormessage',nl2br("extractPropeties is error."));
						$redis->sadd($jobtitle);
                                                break;
					}
				
					$string_csv2libsvm_outputfilename=$task_running_full_path.'/csv2libsvm.libsvm';
					$string_csv2libsvm_command='python '.ML_Path.'/csv2libsvm.py '.$string_extractPropeties_outputfilename.' '.$string_csv2libsvm_outputfilename.' -s -l 12';
					system($string_csv2libsvm_command);
					if(!file_exists($string_csv2libsvm_outputfilename)){
						echo "csv2libsvm is error.\n";
						$redis->set($jobtitle.'_ml_error',"1");
						$redis->append($jobtitle.'_errormessage',nl2br("csv2libsvm is error."));
						$redis->sadd($jobtitle);
                                                breake
					}
				
					$string_svm_scale_outputfilename=$task_running_full_path.'/svm_scale';
					$string_svm_scale_command='svm-scale -r '.ML_Path.'/agodecoy1000_traning.libsvm.range '.$string_csv2libsvm_outputfilename.' > '.$string_svm_scale_outputfilename;
					system($string_svm_scale_command);
					if(!file_exists($string_svm_scale_outputfilename)){
						echo "svm-scale is error.\n";
						$redis->set($jobtitle.'_ml_error',"1");
						$redis->append($jobtitle.'_errormessage',nl2br("svm-scale is error."));
                                                $redis->sadd($jobtitle);
 						break;
					}
				
					$string_svm_predict_outputfilename=$task_running_full_path.'/svm_predict';
					$string_svm_predict_command='svm-predict -b 1 '.$string_svm_scale_outputfilename.' '.ML_Path.'/agodecoy1000_traning.libsvm.model '.$string_svm_predict_outputfilename;
					$output=system($string_svm_predict_command);
					if(!file_exists($string_svm_predict_outputfilename)){
						echo "svm-predict is error.\n";
						$redis->set($jobtitle.'_svm_error',"1");
						$redis->append($jobtitle.'_errormessage',nl2br("svm-predict is error."));
						$redis->sadd($jobtitle);
                                                break;
					}
					else{
						$string_svm_predict_outputfile=fopen($string_svm_predict_outputfilename,"r");
						echo fgets($string_svm_predict_outputfile);
						$string_result=fgets($string_svm_predict_outputfile);
						echo $string_result;
						$result=explode(' ',trim($string_result));
						$redis->set($jobtitle.'_svm_result',$result[0]);
						if($result[0]=='1') {
							$redis->set($jobtitle.'_svm_probability',$result[1]);
						}
						else {
							$redis->set($jobtitle.'_svm_probability',$result[2]);
						}
					}
					break;
				}
				
				while(1){
					if(!file_exists($string_svm_scale_outputfilename)){
						break;
					}
					$string_libsvm2csv_outputfilename=$task_running_full_path.'/libsvm2csv.csv';
					$string_libsvm2csv_command='python '.ML_Path.'/libsvm2csv.py '.$string_svm_scale_outputfilename.' '.$string_libsvm2csv_outputfilename.' 12';
					system($string_libsvm2csv_command);
					if(!file_exists($string_libsvm2csv_outputfilename)){
						echo "libsvm2csv is error.\n";
						$redis->set($jobtitle.'_weka_error',"1");
						$redis->append($jobtitle.'_errormessage',nl2br("libsvm2csv is error."));
						$redis->sadd($jobtitle);
                                                break;
					}
					
					$string_weka_CSVLoader_outputfilename=$task_running_full_path.'/weka.arff';
					$string_weka_CSVLoader_command='java weka.core.converters.CSVLoader '.$string_libsvm2csv_outputfilename.' > '.$string_weka_CSVLoader_outputfilename;
					system($string_weka_CSVLoader_command);
					if(!file_exists($string_weka_CSVLoader_outputfilename)){
						echo "weka_CSVLoader is error.\n";
						$redis->set($jobtitle.'_weka_error',"1");
						$redis->append($jobtitle.'_errormessage',nl2br("weka_CSVLoader is error."));
						$redis->sadd($jobtitle);
                                                break;
					}
					system("sed -i 's/class numeric/class{-1,1}/' ".$string_weka_CSVLoader_outputfilename);
					
					$string_weka_command='java weka.classifiers.trees.RandomForest -p 13 -l '. ML_Path.'/RF.model -T '.$string_weka_CSVLoader_outputfilename.' > '.$task_running_full_path.'/RandomForest.out';
					system($string_weka_command);
					$string_RandomForest_outputfile=fopen($task_running_full_path."/RandomForest.out","r");
					fgets($string_RandomForest_outputfile);
					fgets($string_RandomForest_outputfile);
					echo fgets($string_RandomForest_outputfile);
					fgets($string_RandomForest_outputfile);
					echo fgets($string_RandomForest_outputfile);
					$string_result=fgets($string_RandomForest_outputfile);
					echo $string_result;
					$result=sscanf(trim($string_result),"%d %d:%d %d:%d %s %f");
					$redis->set($jobtitle.'_RandomForest_result',$result[4]);
					if($result[5]=='+'){
						$redis->set($jobtitle.'_RandomForest_probability',$result[6]);
					}
					else {
						$redis->set($jobtitle.'_RandomForest_probability',$result[5]);
					}
	
/*					$string_weka_command='java weka.classifiers.trees.J48 -p 13 -l '.ML_Path.'/J48.model -T '.$string_weka_CSVLoader_outputfilename.' > '.$task_running_full_path.'/J48.out';
					system($string_weka_command);
					$string_J48_outputfile=fopen($task_running_full_path."/J48.out","r");
					fgets($string_J48_outputfile);
					fgets($string_J48_outputfile);
					echo fgets($string_J48_outputfile);
					fgets($string_J48_outputfile);
					echo fgets($string_J48_outputfile);
					$string_result=fgets($string_J48_outputfile);
					echo $string_result;
					$result=sscanf(trim($string_result),"%d %d:%d %d:%d %s %f");
					$redis->set($jobtitle.'_J48_result',$result[4]);
					if($result[5]=='+'){
						$redis->set($jobtitle.'_J48_probability',$result[6]);
					}
					else {
						$redis->set($jobtitle.'_J48_probability',$result[5]);
					}
*/									
					$string_weka_command='java weka.classifiers.lazy.IBk -p 13 -l '.ML_Path.'/IBK.model -T '.$string_weka_CSVLoader_outputfilename.' > '.$task_running_full_path.'/IBK.out';
					system($string_weka_command);
					$string_IBK_outputfile=fopen($task_running_full_path."/IBK.out","r");
					fgets($string_IBK_outputfile);
					fgets($string_IBK_outputfile);
					echo fgets($string_IBK_outputfile);
					fgets($string_IBK_outputfile);
					echo fgets($string_IBK_outputfile);
					$string_result=fgets($string_IBK_outputfile);
					echo $string_result;
					$result=sscanf(trim($string_result),"%d %d:%d %d:%d %s %f");
					$redis->set($jobtitle.'_IBK_result',$result[4]);
					if($result[5]=='+'){
						$redis->set($jobtitle.'_IBK_probability',$result[6]);
					}
					else {
						$redis->set($jobtitle.'_IBK_probability',$result[5]);
					}
					break;
				}
				
				while(1){			
					$string_ligprep_outputfilename=$task_running_full_path.'/glide.mae';
					$string_ligprep_command=ML_Path.'/ligprep.sh -l '.$string_input_file_full_path.' -o '.$string_ligprep_outputfilename;
					system($string_ligprep_command);
					if(!file_exists($string_ligprep_outputfilename)){
						echo "ligprep is error.\n";
						$redis->append($jobtitle.'_errormessage',nl2br("ligprep is error."));
						$redis->sadd($jobtitle);
                                                break;
					}
					
					$string_glide_command=ML_Path.'/glide.sh -l '.$string_ligprep_outputfilename;
					system($string_glide_command);
					$string_glide_outputfile=fopen($task_running_full_path."/glide.txt","r");
					$string_result=fgets($string_glide_outputfile);
					if(strlen(trim($string_result))>0){
						$redis->set($jobtitle.'_glide_score',round(trim($string_result),2));
					}
					else {
						echo "glide is error.\n";
						$redis->append($jobtitle.'_errormessage',nl2br("glide is error."));
                                                $redis->sadd($jobtitle);
					}
					
					$string_phase_outputfilename='phase.AAADPR.8.maegz';
					$string_phase_command='phase_find_matches '.$string_ligprep_outputfilename.' '.ML_Path.'/AAADPR.8 '.$string_phase_outputfilename.' -NOCHECKPOINT -wait';
					system($string_phase_command);
					if(!file_exists($task_running_full_path.'/'.$string_phase_outputfilename.'.log')){
						echo "phase.AAADPR.8.maegz.log is missing.\n";
						$redis->append($jobtitle.'_errormessage',nl2br("phase.AAADPR.8.maegz.log is missing."));
					        $redis->sadd($jobtitle);
                                        }
					else {
						$string_phase_outputfile=fopen($task_running_full_path.'/'.$string_phase_outputfilename.'.log',"r");
						$found=0;
						while(!feof($string_phase_outputfile)) {
							$buff=fgets($string_phase_outputfile);
							if(strncmp($buff,"Number of conformers searched =",31)==0){
								$result=sscanf($buff,"Number of conformers searched = %d");
								if($result[0]>0){
									$redis->set($jobtitle.'_AAADPR.8',"1");
								}
								else {
									$redis->set($jobtitle.'_AAADPR.8',"-1");
								}
								$found=1;
								break;
							}
						}
						if($found==0){
							echo "phase find matches AAADPR.8 is error.\n";
							$redis->append($jobtitle.'_errormessage',nl2br("phase find matches AAADPR.8 is error."));
                                                        $redis->sadd($jobtitle);
						}
						fclose($string_phase_outputfile);
					}
					
					$string_phase_outputfilename='phase.AADPR.32.maegz';
					$string_phase_command='phase_find_matches '.$string_ligprep_outputfilename.' '.ML_Path.'/AADPR.32 '.$string_phase_outputfilename.' -NOCHECKPOINT -wait';
					system($string_phase_command);
					if(!file_exists($task_running_full_path.'/'.$string_phase_outputfilename.'.log')){
						echo "phase.AADPR.32.maegz.log is missing.\n";
						$redis->append($jobtitle.'_errormessage',nl2br("phase.AADPR.32.maegz.log is missing."));
					        $redis->sadd($jobtitle);
                                        }
					else {
						$string_phase_outputfile=fopen($task_running_full_path.'/'.$string_phase_outputfilename.'.log',"r");
						$found=0;
						while(!feof($string_phase_outputfile)) {
							$buff=fgets($string_phase_outputfile);
							if(strncmp($buff,"Number of conformers searched =",31)==0){
								$result=sscanf($buff,"Number of conformers searched = %d");
								if($result[0]>0){
									$redis->set($jobtitle.'_AADPR.32',"1");
								}
								else {
									$redis->set($jobtitle.'_AADPR.32',"-1");
								}
								$found=1;
								break;
							}
						}
						if($found==0){
							echo "phase find matches AADPR.32 is error.\n";
							$redis->append($jobtitle.'_errormessage',nl2br("phase find matches AADPR.32 is error."));
						}
						fclose($string_phase_outputfile);
					}
					
					$string_phase_outputfilename='phase.AADPR.229.maegz';
					$string_phase_command='phase_find_matches '.$string_ligprep_outputfilename.' '.ML_Path.'/AADPR.229 '.$string_phase_outputfilename.' -NOCHECKPOINT -wait';
					system($string_phase_command);
					if(!file_exists($task_running_full_path.'/'.$string_phase_outputfilename.'.log')){
						echo "phase find matches AADPR.229 is error.\n";
						$redis->append($jobtitle.'_errormessage',nl2br("phase find matches AADPR.229 is error."));
					        $redis->sadd($jobtitle);
                                        }
					else {
						$string_phase_outputfile=fopen($task_running_full_path.'/'.$string_phase_outputfilename.'.log',"r");
						$found=0;
						while(!feof($string_phase_outputfile)) {
							$buff=fgets($string_phase_outputfile);
							if(strncmp($buff,"Number of conformers searched =",31)==0){
								$result=sscanf($buff,"Number of conformers searched = %d");
								if($result[0]>0){
									$redis->set($jobtitle.'_AADPR.229',"1");
								}
								else {
									$redis->set($jobtitle.'_AADPR.229',"-1");
								}
								$found=1;
								break;
							}
						}
						if($found==0){
							echo "phase find matches AADPR.229 is error.\n";
							$redis->append($jobtitle.'_errormessage',nl2br("phase find matches AADPR.229 is error."));
						}
						fclose($string_phase_outputfile);
					}
					
					$string_phase_outputfilename='phase.AADPRR.1667.maegz';
					$string_phase_command='phase_find_matches '.$string_ligprep_outputfilename.' '.ML_Path.'/AADPRR.1667 '.$string_phase_outputfilename.' -NOCHECKPOINT -wait';
					system($string_phase_command);
					if(!file_exists($task_running_full_path.'/'.$string_phase_outputfilename.'.log')){
						echo "phase find matches AADPRR.1667 is error.\n";
						$redis->append($jobtitle.'_errormessage',nl2br("phase find matches AADPRR.1667 is error."));
					        $redis->sadd($jobtitle);
                                        }
					else {
						$string_phase_outputfile=fopen($task_running_full_path.'/'.$string_phase_outputfilename.'.log',"r");
						$found=0;
						while(!feof($string_phase_outputfile)) {
							$buff=fgets($string_phase_outputfile);
							if(strncmp($buff,"Number of conformers searched =",31)==0){
								$result=sscanf($buff,"Number of conformers searched = %d");
								if($result[0]>0){
									$redis->set($jobtitle.'_AADPRR.1667',"1");
								}
								else {
									$redis->set($jobtitle.'_AADPRR.1667',"-1");
								}
								$found=1;
								break;
							}
						}
						if($found==0){
							echo "phase find matches AADPRR.1667 is error.\n";
							$redis->append($jobtitle.'_errormessage',nl2br("phase find matches AADPRR.1667 is error."));
						}
						fclose($string_phase_outputfile);
					}
					
					$redis->sadd('finished',$jobtitle);
					
					
					break;
				}
			}
		}
		
		echo "Current Time: " . date(DATE_RFC822) . "\n";
		echo "job(".$jobtitle.") is finished.\n" ;
		$redis->set($jobtitle.'_finishedtime',date("Y-m-d/H:i:s"));
		$jobs_waiting = $redis->llen('submited');
	}
	echo "Waiting for new jobs\n";
	$redis->subscribe("newjob",function($client,$channel,$message){
		$client->unsubscribe();
	});
}
while(1);

?>
