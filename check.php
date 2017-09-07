<?php  
	include "header.php";
?>

<div class="content">
<div class="top-post">

<?php
if(isset($_SESSION['login'])){
	if($_SESSION['login']==1){
		echo '<table border="1" cellpadding="1" cellspacing="1">
		<tr>
		<th>id</th>
		<th>title</th>
		<th>status</th>
		<!--<th>error</th>-->
		<th>result</th>
		<th>file</th>
		<th>time_uploaded</th>
		<th>time_begin</th>
        <th>time_finished</th>
		</tr>';
		
		
		
        $username=$_SESSION['username'];
		$title = $redis->sort($username.'_jobs','by','*_submitedtime','DESC','ALPHA');
		for($i=0;$i<count($title);++$i){
			echo "<tr>";
			
			echo "<td>" . $i . "</td>";
			
			echo "<td>" . $redis->get($title[$i].'_title') . "</td>";
			
			echo "<td>";
			if ($redis->sismember('error',$title[$i]))
				echo "error" ;
			elseif ($redis->sismember('finished',$title[$i]))
				echo "finished" ;
			elseif($redis->sismember('running',$title[$i]))
				echo "computing" ;
			else
				echo "waiting"; 
			echo "</td>";
			
			echo "<td><a href='result.php?task_title=". $title[$i] ."'>result</a></td>";
			
			$file = $redis->get($title[$i]);
			echo "<td>" . $file . "</td>";
			
//			$num_of_finished_protein = $redis->scard($title[$i].'_proteinfinished');
//			echo "<td>" . $num_of_finished_protein . "</td>";


			$time_uploaded = $redis->get($title[$i].'_submitedtime');
			echo "<td>" . $time_uploaded . "</td>";
			
                        $time_begin = $redis->get($title[$i].'_begintime');
                        echo "<td>" . $time_begin . "</td>";
			
                        $time_finished = $redis->get($title[$i].'_finishedtime');
			echo "<td>" . $time_finished . "</td>";
			
			echo "</tr>"; 
		}
	}
	else{
		echo "<p>Please sign in.</p>";
	}
}
else
{
	echo "<p>Please sign in.</p>";
}
?>




</table>
</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
