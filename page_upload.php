<?php  
	include "header.php";
?>
<script src="jquery-1.11.3.min.js">
</script>
<script type="text/javascript">
$(document).ready(function(){

});

</script>

<div class="content">

<div class="top-post">

<?php
if(isset($_SESSION['login'])){
	if($_SESSION['login']==1){
        echo '<form id="task_submit" action="php_upload_insert_db.php" method="post" enctype="multipart/form-data">
		<p><label for="task_title">Job Title:</label>
		<input name="task_title" type="text" id="task_title" value="title_'.date('ymdHis').'" size="40" maxlength="255" />
		</p>
		<p>&nbsp; </p>
		<p>
		  <label for="task_uploaded_file">Molecule File:</label>
		  <input type="file" name="task_uploaded_file" accept="/sdf,/mol" /> | <a href="./bam01.sdf" target="_blank">Sample File 1</a></p>
		  <p><small>File : The molecular structure file(sdf file).</small></p>
		<p>&nbsp;</p>'
		.'<p>
		  <button type="submit">submit</button>
		</p>
		<p>&nbsp;</p>';
                
        echo '</form>';
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
</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
