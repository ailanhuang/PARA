<?php  
	include "header.php";
    $jobtitle=$_GET['task_title'];
?>
<script src="../jquery-1.11.3.min.js">
</script>
<script type="text/javascript">
$(document).ready(function(){
  $("tr.uniprot_other").hide();
  $("button.expand").click(function(){
    var expand=$(this).get(0);
    var expandvalue=expand.value;
    var itemname=".tr-"+expandvalue;
    if($(this).text()=="+"){
      $(itemname).show();
      $(this).text("-");
    }
    else{
      $(itemname).hide();
      $(this).text("+");
    }
  });
  $("button.score-sort").click(function(){
      var url="score-sort.php";
      var jobtitle=(<?php echo json_encode($jobtitle)?>).toString();
      url=url+"?jobtitle="+jobtitle;
      var value=$(this).get(0).value;
      url=url+"&ligand="+value;
      var id="#tbody-score-"+value;
      $(id).load(url);
  });
  $("button.specific-sort").click(function(){
      var url="specific-sort.php";
      var jobtitle=(<?php echo json_encode($jobtitle)?>).toString();
      url=url+"?jobtitle="+jobtitle;
      var value=$(this).get(0).value;
      url=url+"&ligand="+value;
      var id="#tbody-score-"+value;
      $(id).load(url);
  });
});
</script>
<div class="content">

<div class="top-post">

<?php
require '../Client.php';
$redis = new Credis_Client('localhost');

echo "<div style='color:#999'><p style='font-size:20px;color:#000'>Command:";
$command=$redis->get($jobtitle.'_command');
$type=$redis->get($jobtitle.'_glide_type');
echo "<p style='color:#666'>".$command." ".$type."</p>";
echo "</p></div>";
echo "<p>&nbsp</p>";

$resultfile=$redis->get($jobtitle.'_resultfile');
echo "<p style='font-size:20px;'>File link:</p>";
if(strlen($resultfile)==0)
	echo "<p></p>";
else
	echo "<p>&nbsp</p><p><a href='download/".$redis->get($jobtitle.'_resultfile')."' target='_blank'>download</a></p>";
echo "<p>&nbsp</p>";

echo "<p style='font-size:20px;'>Score:</p>";
echo "<p id='result'>&nbsp</p>";
$ligands=$redis->smembers($jobtitle.'_ligands');
for($i=0;$i<count($ligands);$i++)
{
	echo "<p style='color:#666'>&nbsp&nbsp". $ligands[$i] . ":</p>";
	echo "<table border='1' cellpadding='1' cellspacing='1'>";
	echo "<tr>";
	echo "<th>&nbsp</th>";
        echo "<th>id</th>";
	echo "<th>pdb_id</th>";
	echo '<th>score<button type="button" class="score-sort" value="'.$ligands[$i].'">sort</button></th>';
        echo '<th>specific value<button type="button" class="specific-sort" value="'.$ligands[$i].'">sort</button></th>';
        echo "<th >Target_Full_Name</th>";
        echo "<th >Diseases</th>";
        echo "<th>Ligand_Score</th>";
        echo "<th>Ligand_Potency</th>";
        echo "<th>ligand_rmsd</th>";
	echo "</tr>";
        echo '<tbody class="tb-score" id="tbody-score-'.$ligands[$i].'">';
        $cache=$redis->setnx('cache_'.$jobtitle.'_'.$ligands[$i].'_score_sort',1);
        if($cache>0){
            $redis->expire('cache_'.$jobtitle.'_'.$ligands[$i].'_score_sort',600);
            $uniprot=$redis->smembers($jobtitle.'_'.$ligands[$i].'_uniprot');
            for($j=0;$j<count($uniprot);$j=$j+1){
                $uniprot_score=$redis->sort($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j],'by',$jobtitle.'_'.$ligands[$i].'_*','get',$jobtitle.'_'.$ligands[$i].'_*','limit','0','1');
                $redis->set($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score',$uniprot_score);
                $redis->expire($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score','600');
            }
            $redis->sort($jobtitle.'_'.$ligands[$i].'_uniprot','by',$jobtitle.'_'.$ligands[$i].'_*_score','store',$jobtitle.'_'.$ligands[$i].'_uniprot_score_sort');
            $redis->expire($jobtitle.'_'.$ligands[$i].'_uniprot_score_sort','600');
        }
	$uniprot=$redis->lrange($jobtitle.'_'.$ligands[$i].'_uniprot_score_sort','0','-1');
	for($j=0;$j<count($uniprot);$j=$j+1){
		echo "<tr>";
		echo '<td>';
                $num=$redis->scard($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j]);
                if($num>1)
                    echo '<button type="button" class="expand" id="button-'.$ligands[$i].'-'.$uniprot[$j].'" value="'.$ligands[$i].'-'.$uniprot[$j].'">+</button>';
                else
                    echo "&nbsp";
                echo "</td>";
                if($cache>0){
		    $redis->sort($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j],'by',$jobtitle.'_'.$ligands[$i].'_*','get','#','get',$jobtitle.'_'.$ligands[$i].'_*','get',$jobtitle.'_'.$ligands[$i].'_*_specific','limit','0','1','store',$jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score_sort_top');
                    $redis->expire($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score_sort_top','600');
		}
                $uniprot_score=$redis->lrange($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score_sort_top','0','-1');
                $id=$j+1;
                echo "<td>". $id . "</td>";
                echo "<td>" . $uniprot_score[0] . "</td>";
		echo "<td>" . $uniprot_score[1] . "</td>";
                echo "<td>" . $uniprot_score[2] . "</td>";
		$pdb_uid=$redis->hget('pdb_id',$uniprot_score[0]);
                $target_full_name=$redis->get($pdb_uid.'_target_full_name');
                echo "<td>" . $target_full_name . "</td>";
		$disease=$redis->get($pdb_uid.'_diseases');
		echo "<td>" . $disease . "</td>";
		$docking_score=$redis->get($pdb_uid.'_docking_score');
		echo "<td>" . $docking_score . "</td>";
		$ligand_potency=$redis->get($pdb_uid.'_ligand_potency');
		echo "<td>" . $ligand_potency . "</td>";
                $ligand_rmsd=$redis->get($pdb_uid.'_rmsd');
                echo "<td>" . $ligand_rmsd . "</td>";
		echo "</tr>";
                if($cache>0){
                    $redis->sort($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j],'by',$jobtitle.'_'.$ligands[$i].'_*','get','#','get',$jobtitle.'_'.$ligands[$i].'_*','get',$jobtitle.'_'.$ligands[$i].'_*_specific','limit','1','-1','store',$jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score_sort_other');
                    $redis->expire($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score_sort_other','600');
                }
                $ddb_score=$redis->lrange($jobtitle.'_'.$ligands[$i].'_'.$uniprot[$j].'_score_sort_other','0','-1');
                for($k=0;$k<count($ddb_score);$k=$k+3){
                    echo '<tr class="uniprot_other tr-'.$ligands[$i].'-'.$uniprot[$j].'">';
                    echo "<td>&nbsp</td>";
                    echo "<td>&nbsp</td>";
                    echo "<td>" . $ddb_score[$k] . "</td>";
                    echo "<td>" . $ddb_score[$k+1] . "</td>";
                    echo "<td>" . $ddb_score[$k+2] . "</td>";
                    $pdb_uid=$redis->hget('pdb_id',$ddb_score[$k]);
                    $target_full_name=$redis->get($pdb_uid.'_target_full_name');
                    echo "<td>" . $target_full_name . "</td>";
                    $disease=$redis->get($pdb_uid.'_diseases');
                    echo "<td>" . $disease . "</td>";
                    $docking_score=$redis->get($pdb_uid.'_docking_score');
                    echo "<td>" . $docking_score . "</td>";
                    $ligand_potency=$redis->get($pdb_uid.'_ligand_potency');
                    echo "<td>" . $ligand_potency . "</td>";
                    $ligand_rmsd=$redis->get($pdb_uid.'_rmsd');
                    echo "<td>" . $ligand_rmsd . "</td>";
                    echo "</tr>";
                }
	}
	echo "</tr>";
        echo '</tbody>';
	echo "</table>";
}

echo "<div style='color:#999'><p style='font-size:20px;color:#000'>errorlog:";
echo "<p>&nbsp</p>";
$errorlog=$redis->get($jobtitle.'_errormessage');
if(strlen($errorlog)==0)
  $errorlog="<p>NULL</p>";
echo "<p style='color:#666'>".nl2br($errorlog)."</p>";
echo "</p></div>";

?>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
