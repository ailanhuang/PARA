<?php 
	session_start();
	$_SESSION['login']=1;
	$_SESSION['username']='test';
        require 'Client.php';
	$redis = new Credis_Client('localhost');
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>PARA</title>


<link rel="stylesheet" href="style.css" type="text/css" media="screen" />

<link href='http://fonts.googleapis.com/css?family=PT+Serif:regular,bold' rel='stylesheet' type='text/css' />
<link href='http://fonts.googleapis.com/css?family=Crushed' rel='stylesheet' type='text/css' />

<style type="text/css" media="screen">
	html { margin-top: 28px !important; }
	* html body { margin-top: 28px !important; }
</style>

</head>

<body>

<div id="outerdiv" >

<div id="wrapper"> 

<div id="masthead">

<div id="top"> 
	<div class="blogname">
	<h1><a href="index.php" title="PARA">PARA</a></h1>
		<h2>A Server to Predict β2 Adrenergic Receptor Agonist</h2>
	</div>

</div><!-- END topmenu -->

<div id="botmenu">
	<div id="submenu" class="menu-%e8%8f%9c%e5%8d%951-container"><ul id="menu-%e8%8f%9c%e5%8d%951" class="sfmenu">
    <li id="menu-item-4" class="menu-item menu-item-type-custom menu-item-object-custom current-menu-item current_page_item menu-item-home menu-item-4"><a href="index.php">Home</a></li>
	<li id="menu-item-5" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="page_upload.php">Upload</a></li>
    <li id="menu-item-6" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="check.php">Check</a></li>
    <li id="menu-item-7" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="help.php">Help & Contact</a></li>
<?php 
/*if(isset($_SESSION['login'])){
    if($_SESSION['login']==0)
       echo '<li id="menu-item-9" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-9"><a href="logon.php">Sign In</a></li>';
    else{
       echo '<li id="menu-item-10" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-10"><a href="passwd.php">Change Password</a></li>';
       echo '<li id="menu-item-11" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-11"><a href="logout.php">Log Out</a></li>';
    }
}
else{
	echo '<li id="menu-item-12" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-12"><a href="logon.php">Sign In</a></li>';
}*/
?>
</ul></div></div><!-- END botmenu -->

</div>
