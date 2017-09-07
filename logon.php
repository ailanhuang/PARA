<?php  
	include "header.php";
?>
<div class="content">
  <div class="top-post">
<?php
if($_SESSION['login']==0)
    echo '<form id="password" action="login.php" method="post" enctype="multipart/form-data">
    <table>
    <tr><td>Username:
    <input type="text" name="username"></td></tr>
    <tr><td>Password:
    <input type="password" name="password">
    </td></tr>
    </table>
    <p><button type="submit">OK</button></p>
    </form>';
else
    echo '<p>You have log in.</p>';

?>
</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
