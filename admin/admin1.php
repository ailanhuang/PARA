<?php  
	include "header.php";
?>
<script type="text/javascript">
function selectAllorNone(obj,cName){
  var checkboxs = document.getElementsByName(cName);
    for(var i=0;i<checkboxs.length;++i){
      checkboxs[i].checked=obj.checked;
    }
}
</script>
<div class="content">

<div class="top-post">
<?php

require '../Client.php';

$redis = new Credis_Client('localhost');

if(isset($_SESSION['adminlogin'])){
	if($_SESSION['adminlogin']==0){
		$password = $_POST['password'];

		if($password == $redis->get('adminpasswd')){
			$_SESSION['adminlogin']=1;
                        echo "<p>Welcome administrator.</p>";
		}
		else{
			$_SESSION['adminlogin']=0;
                        echo '<p>password is error.</p>';
		}
	}
}
else{
	$password = $_POST['password'];

	if($password == $redis->get('adminpasswd')){
		$_SESSION['adminlogin']=1;
                echo "<p>Welcome administrator.</p>";
	}
	else{
		$_SESSION['adminlogin']=0;
                echo '<p>password is error.</p>';
	}
}

echo "</table>
</form>";

?>


</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
