<?php
        include "header.php";
?>
<div class="content">
  <div class="top-post">
<?php
if(isset($_SESSION['login'])){
    if($_SESSION['login']==1){
        $username=$_SESSION['username'];
        $email=$redis->hget('userinfo_'.$username,'email');
        echo '<form id="password" action="passwd_change.php" method="post" enctype="multipart/form-data">
        <table width="650" border="0">
        <tr><td width="150">E-Mail:</td>
        <td><input type="text" name="email" value="'.$email.'"></td></tr>
        <tr><td>New Password:</td>
        <td><input type="password" name="password"></td></tr>
        <tr><td>Confirm Password:</td>
        <td><input type="password" name="password_again"></td></tr>
        </table>
        <p><button type="submit">Submit</button> 
        </p>
        </form>';
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

