<?php 
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

<title>DockingDataBank Server</title>


<link rel="stylesheet" href="../style.css" type="text/css" media="screen" />

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
	<h1><a href="index.php" title="DockingDataBank">DockingDataBank</a></h1>
		<h2>&nbsp;</h2>
	</div>

</div><!-- END topmenu -->

<div id="botmenu">
	<div id="submenu" class="menu-%e8%8f%9c%e5%8d%951-container"><ul id="menu-%e8%8f%9c%e5%8d%951" class="sfmenu">
	<li id="menu-item-5" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="page_upload.php">Upload</a></li>
    <li id="menu-item-5" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="check.php">Jobs</a></li>
    <li id="menu-item-5" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="user.php">Users</a></li>
<?php 
if(isset($_SESSION['adminlogin'])){
    if($_SESSION['adminlogin']==0)
       echo '<li id="menu-item-5" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="admin.php">Sign In</a></li>';
    else
       echo '<li id="menu-item-5" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="logout.php">Log Out</a></li>';
}
else{
	echo '<li id="menu-item-5" class="menu-item menu-item-type-post_type menu-item-object-page menu-item-5"><a href="admin.php">Sign In</a></li>';
}
?>
</ul></div></div><!-- END botmenu -->

</div>
