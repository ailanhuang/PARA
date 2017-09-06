<?php
$disease=$_GET["disease"];

require 'redis.php';
use phpish\redis;

$redis = redis\client();

$sscan_result=array();
$pdbid=array();
$uniprot=array();
$uniprot_id;
$cursor='0';
while(1)
{
    $sscan_result = $redis('sscan diseases_table '.$cursor.' match "*'.$disease.'*" count 1000');

    for($i=0;$i<count($sscan_result[1]);$i++)
    {
        $uniprot_id=strtok($sscan_result[1][$i],'|');
        $pdbid=$redis('lrange uniprot_'.$uniprot_id.' 0 -1');
        for($j=0;$j<count($pdbid);$j++){
            $uid=$redis('hget pdb_id '.$pdbid[$j]);
            array_push($uniprot,$uid);
        }
    }
    if($sscan_result[0]!=0)
       $cursor=$sscan_result[0];
    else
       break;
}
echo json_encode($uniprot);
?>
