<?php  
	include "header.php";

?>

<div class="content">

<div class="top-post">


<?php

require '../Client.php';

$jobs=$_POST['jobs'];
$redis = new Credis_Client('localhost');
for($i=0;$i<count($jobs);++$i){
	$redis->del($jobs[$i]);
	$redis->del($jobs[$i].'_moleculefile');
	$redis->del($jobs[$i].'_proteinselect');
	$redis->del($jobs[$i].'_submitedtime');
	$redis->del($jobs[$i].'_finishedtime');
	$redis->del($jobs[$i].'_resultfile');
	$redis->del($jobs[$i].'_command');
	$redis->del($jobs[$i].'_title');
	$redis->del($jobs[$i].'_errormessage');
	$redis->del($jobs[$i].'_begintime');
        $redis->del($jobs[$i].'_glide_type');
	$ligands=$redis->smembers($jobs[$i].'_ligands');
	for($j=0;$j<count($ligands);$j=$j+1){
		$redis->del($_GET['task_title'].$ligands[$j]);
                $pdbid=$redis->smembers($jobs[$i].'_'.$ligands[$j].'_proteinfinished');
                for($pi=0;$pi<count($pdbid);$pi=$pi+1)
                    $redis->del($jobs[$i].'_'.$ligands[$j].'_'.$pdbid[$pi]);
                $redis->del($jobs[$i].'_'.$ligands[$j].'_proteinfinished');
                $uniprot=$redis->smembers($jobs[$i].'_'.$ligands[$j].'_uniprot');
                for($ui=0;$ui<count($uniprot);$ui=$ui+1)
                    $redis->del($jobs[$i].'_'.$ligands[$j].'_'.$uniprot[$ui]);
                $redis->del($jobs[$i].'_'.$ligands[$j].'_uniprot');
                $redis->del($jobs[$i].'_'.$ligands[$j].'_uniprot_sort');
	}
	$redis->del($jobs[$i].'_ligands');
	$redis->lrem('submited',0,$jobs[$i]);
	$redis->srem('running',$jobs[$i]);
	$redis->srem('finished',$jobs[$i]);
        $username=$redis->get($jobs[$i].'_user');
        $redis->del($jobs[$i].'_user');
        $redis->srem($jobs[$i].'_jobs',$jobs[$i]);
}
echo "<p>jobs is deleted.</p>";
?>


</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
