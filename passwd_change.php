<?php
        include "header.php";
?>
<script type="text/javascript">
</script>
<div class="content">

<div class="top-post">
<?php

if(isset($_SESSION['login'])){
    if($_SESSION['login']==1){

        require 'Client.php';

        $redis = new Credis_Client('localhost');
        $password = $_POST['password'];
        $username = $_SESSION['username'];
        $email=$_POST['email'];
        $password_again=$_POST['password_again'];

        if(strlen($password)>0){
            if($password==$password_again){
                $redis->hset('passwords',$username,$password);
                $redis->set($username.'_email',$email);
                echo "<p>Now, your password has been changed.</p>";
            }
            else{
                echo "<p>The password does not match the confirm password.</p>";
            }
        }
        else{
            echo "<p>Password is requirie.</p>";
        }

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
