<?php  
	include "header.php";
?>
<script type="text/javascript">
</script>
<div class="content">

<div class="top-post">
<?php

require 'Client.php';

$redis = new Credis_Client('localhost');
if(isset($_SESSION['login'])){
	if($_SESSION['login']==0){
		$password = $_POST['password'];
                $username = $_POST['username'];

                 
		if($redis->hexists('passwords',$username)==1 and $password == $redis->hget('passwords',$username)){
			echo "<p>Now, you can submit jobs.</p>";
			$_SESSION['login']=1;
                        $_SESSION['username']=$username;
		}
		else{
			echo "<p>Username or password error.</p>";
			$_SESSION['login']=0;
		}
	}
}
else{
	$password = $_POST['password'];
        $username = $_POST['username'];

	if($redis->hexists('passwords',$username)==1 and $password == $redis->hget('passwords',$username)){
		echo "<p>Now, you can submit jobs.</p>";
		$_SESSION['login']=1;
                $_SESSION['username']=$username;
	}
	else{
		echo "<p>Username or password error.</p>";
		$_SESSION['login']=0;
	}
}

?>


</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
