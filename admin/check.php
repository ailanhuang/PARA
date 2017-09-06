<?php  
	include "header.php";
?>
<script src="../jquery-1.11.3.min.js">
</script>
<script type="text/javascript">
$(document).ready(function(){
  $("#All").click(function(){
    var checkboxs=$(":checkbox").toArray();
    var allcheckboxs=$("#All").get(0);
    for(var i=0;i<checkboxs.length;i++)
    {
      checkboxs[i].checked=allcheckboxs.checked;
    }
  });
});
</script>
<div class="content">
<div class="top-post">

<?php
if(isset($_SESSION['adminlogin'])){
	if($_SESSION['adminlogin']==1){
            echo  '<form id="jobadmin" action="admin_exec.php" method="post" enctype="multipart/form-data">
            <p>
            <button type="submit">DEL</button>
            </p>';

		echo '<table border="1" cellpadding="1" cellspacing="1">
		<tr>
                <th width="44"><input name="All" type="checkbox" id="All"  value="" /></th>
		<th>title</th>
                <th>user</th>
		<th>status</th>
		<th>result</th>
		<th>file</th>
		<th>protein</th>
		<th>time_uploaded</th>
		<th>time_begin</th>
                <th>time_finished</th>
		</tr>';
		
		require '../Client.php';
		$redis = new Credis_Client('localhost');
		
		$title_submited = $redis->lrange('submited',0,-1);
		$redis->sunionstore('jobs','running','finished','error');
		$title_running_finished = $redis->sort('jobs','by','*_submitedtime','DESC','ALPHA');
		$title = array_merge($title_submited,$title_running_finished);
		for($i=0;$i<count($title);++$i){
			echo "<tr>";
			
                        echo '<td><input name="jobs[]" type="checkbox" value="' . $title[$i] . '"  /></td>';

			echo "<td>" . $redis->get($title[$i].'_title') . "</td>";

                        echo '<td>' . $redis->get($title[$i].'_user') . "</td>";
			
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
			
			$num_of_protein = $redis->scard($title[$i].'_proteinselect');
			echo "<td>" . $num_of_protein . "</td>";
			
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




</table></form>
</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
