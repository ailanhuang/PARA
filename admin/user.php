<?php  
	include "header.php";
?>
<script src="../jquery-1.11.3.min.js">
</script>
<script type="text/javascript">
$(document).ready(function(){
  $(".changepassword").click(function(){
    var username=$(this).val();
    var input_passwd=username+"_new_password";
    var password=$("#"+input_passwd).val();
    var url="changepasswd.php";
    var url=url+"?username="+username;
    var url=url+"&password="+password;
    var id=username+"_result";
    $("#"+id).load(url);
//    var text=password+username;
//    var id=username+"_result";
//    $("#"+id).html(text);
  });
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
            echo  '<form id="jobadmin" action="deluser.php" method="post" enctype="multipart/form-data">
            <p>
            <button type="submit">DEL</button>
            </p>';

		echo '<table border="1" cellpadding="1" cellspacing="1">
		<tr>
                <th width="44"><input name="All" type="checkbox" id="All" value="" /></th>
                <th>username</th>
                <th>name</th>
                <th>title</th>
                <th>institute</th>
                <th>country</th>
		<th>change password</th>
		</tr>';
		
		require '../Client.php';
		$redis = new Credis_Client('localhost');
		
		$username = $redis->hkeys('passwords');
		for($i=0;$i<count($username);++$i){
			echo "<tr>";
			
                        echo '<td><input name="users[]" type="checkbox" value="' . $username[$i] . '" /></td>';

			echo "<td>" . $username[$i] . "</td>";
                        $name=$redis->hget('userinfo_'.$username[$i],'firstname');
                        $name=$name.' '.$redis->hget('userinfo_'.$username[$i],'middlename');
                        $name=$name.' '.$redis->hget('userinfo_'.$username[$i],'lastname');
                        echo "<td>" . $name . "</td>";
                        $title=$redis->hget('userinfo_'.$username[$i],'title');
                        echo "<td>" . $title . "</td>";
                        $institute=$redis->hget('userinfo_'.$username[$i],'institute');
                        echo "<td>" . $institute . "</td>";
                        $country=$redis->hget('userinfo_'.$username[$i],'country');
                        echo "<td>" . $country . "</td>";
                        
                        echo '<td><input name="password" type="password" id="'.$username[$i].'_new_password" /><button class="changepassword" type="button" value="'.$username[$i].'">OK</button><strong id="'.$username[$i].'_result"></strong></td>';

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
