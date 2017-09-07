<?php  
	include "header.php";
?>

<div class="content">

<div class="top-post">


<?php


function spamcheck($field){
  //filter_var() sanitizes the e-mail 
  //address using FILTER_SANITIZE_EMAIL
  $field=filter_var($field, FILTER_SANITIZE_EMAIL);
  
  //filter_var() validates the e-mail
  //address using FILTER_VALIDATE_EMAIL
  if(filter_var($field, FILTER_VALIDATE_EMAIL)){
    return TRUE;
  }
  else{
    return FALSE;
  }
}

if(isset($_FILES['task_uploaded_file'])) {
    if($_FILES['task_uploaded_file']['error'] == 0) {
        $jobtitle = $_POST['task_title'];
		$jobid= $jobtitle . rand(1, 10000);
		$redis->set($jobid,$_FILES['task_uploaded_file']['name']);
		$redis->set($jobid.'_title',$jobtitle);
		$redis->set($jobid.'_moleculefile',file_get_contents($_FILES['task_uploaded_file']['tmp_name']));
		$redis->set($jobid.'_submitedtime',date("Y-m-d/H:i:s"));

		echo "Uploaed successfully! Done! <br/> Job Serial: ". $_POST['task_title'] . "<br/>";
		echo "Please click to go to the check page!";
		
	    $username=$_SESSION['username'];
        $redis->sadd($username.'_jobs',$jobid);
        $redis->lpush('submited',$jobid);
		$mail = $redis->get($username.'_email');
        $redis->set($jobid.'_user',$username);
		$redis->publish("newjob",$jobid);
		if (isset($mail)){
			//if "email" is filled out, proceed
			//check if the email address is invalid
			$mailcheck = spamcheck($mail);
			if ($mailcheck==TRUE){
				//send email
				$redis->set($jobid.'_emailaddress',$mail);
				echo "<br/>When your job finished, we will send E-mail to " . $mail . " to notify you of your job info.";
			}
			else{
				echo "<br/>Your E-mail address is wrong. Sorry, we will not send the e-mail to notify you of your job info.";
			}
		}
		else{
			echo "<br/>You have not left your E-mail address . Sorry, we will not send the e-mail to notify you of your job info.";
		}
	}
	else{
		echo "file uploaded error!";	
	}		
}
else{
	echo "file not selected!";
}

?>


</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
