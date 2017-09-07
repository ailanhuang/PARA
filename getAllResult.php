<?php
$jobtitle=$_GET["jobtitle"];
$ligendname=$_GET["ligendname"];
$uniprot=$_GET["uniprot"];

require 'Client.php';
$redis = new Credis_Client('localhost');


$ddb_score=$redis->sort($jobtitle.'_'.$ligendname.'_'.$uniprot,'by',$jobtitle.'_'.$ligendname.'_*','get','#','get',$jobtitle.'_'.$ligendname.'_*','limit','1','-1');
for($i=0;$i<count($ddb_score);$i=$i+2){
	echo "<tr>";
	echo "<td>&nbsp</td>";
	echo "<td>&nbsp</td>";
        echo "<td>" . $ddb_score[$i] . "</td>";
	echo "<td>" . $ddb_score[$i+1] . "</td>";
        $pdb_uid=$redis->hget('pdb_id',$ddb_score[$i]);
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

?>
