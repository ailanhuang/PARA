<?php
require '../Client.php';
$redis = new Credis_Client('localhost');

$jobtitle=$_GET["jobtitle"];
$ligand=$_GET["ligand"];

    $cache=$redis->setnx('cache_'.$jobtitle.'_'.$ligand.'_score_sort',1);
    if($cache>0){
        $redis->expire('cache_'.$jobtitle.'_'.$ligand.'_score_sort',600);
        $uniprot=$redis->smembers($jobtitle.'_'.$ligand.'_uniprot');
        for($j=0;$j<count($uniprot);$j=$j+1){
            $uniprot_score=$redis->sort($jobtitle.'_'.$ligand.'_'.$uniprot[$j],'by',$jobtitle.'_'.$ligand.'_*','get',$jobtitle.'_'.$ligand.'_*','limit','0','1');
            $redis->set($jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score',$uniprot_score);
            $redis->expire($jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score','600');
        }
        $redis->sort($jobtitle.'_'.$ligand.'_uniprot','by',$jobtitle.'_'.$ligand.'_*_score','store',$jobtitle.'_'.$ligand.'_uniprot_score_sort');
        $redis->expire($jobtitle.'_'.$ligand.'_uniprot_score_sort','600');
    }
    $uniprot=$redis->lrange($jobtitle.'_'.$ligand.'_uniprot_score_sort','0','-1');
    for($j=0;$j<count($uniprot);$j=$j+1){
        echo "<tr>";
        echo '<td>';
        $num=$redis->scard($jobtitle.'_'.$ligand.'_'.$uniprot[$j]);
        if($num>1)
            echo '<button type="button" class="expand" id="button-'.$ligand.'-'.$uniprot[$j].'" value="'.$ligand.'-'.$uniprot[$j].'">+</button>';
        else
            echo "&nbsp";
        echo '</td>';
        if($cache>0){
           $redis->sort($jobtitle.'_'.$ligand.'_'.$uniprot[$j],'by',$jobtitle.'_'.$ligand.'_*','get','#','get',$jobtitle.'_'.$ligand.'_*','get',$jobtitle.'_'.$ligand.'_*_specific','limit','0','1','store',$jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score_sort_top');
            $redis->expire($jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score_sort_top','600');
        }
        $uniprot_score=$redis->lrange($jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score_sort_top','0','-1');
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
            $redis->sort($jobtitle.'_'.$ligand.'_'.$uniprot[$j],'by',$jobtitle.'_'.$ligand.'_*','get','#','get',$jobtitle.'_'.$ligand.'_*','get',$jobtitle.'_'.$ligand.'_*_specific','limit','1','-1','store',$jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score_sort_other');
            $redis->expire($jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score_sort_other','600');
        }
        $ddb_score=$redis->lrange($jobtitle.'_'.$ligand.'_'.$uniprot[$j].'_score_sort_other','0','-1');
        for($k=0;$k<count($ddb_score);$k=$k+3){
            echo '<tr class="uniprot_other tr-'.$ligand.'-'.$uniprot[$j].'">';
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

});
</script>

