<?php
        include "header.php";

?>

<div class="content">

<div class="top-post">


<?php

require '../Client.php';

$usernames=$_POST['users'];
$redis = new Credis_Client('localhost');
for($i=0;$i<count($usernames);++$i){
    $redis->hdel('userinfo_'.$usernames[$i]);
    $redis->hdel('passwords',$usernames[$i]);
}
echo "<p>users are deleted.</p>";
?>


</div>
<div class="clear"></div>
</div>
</div>
<?php
        include "footer.php";
?>

