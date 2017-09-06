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

<form id="password" action="admin1.php" method="post" enctype="multipart/form-data">密码：
<input type="password" name="password"><button type="submit">ok</button>

</form>
</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
