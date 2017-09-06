<?php  
	include "header.php";
  require '../Client.php';
  $redis = new Credis_Client('localhost');
  $diseases_variety=$redis->smembers("diseases_variety");
?>
<script src="../jquery-1.11.3.min.js">
</script>
<script type="text/javascript">
$(document).ready(function(){
var selectDiseases=0;
var diseasesCount=eval(<?php echo json_encode(count($diseases_variety));?>);
  $("#disease_select").click(function(){
    var url="selectDisease.php";
    var disease=$("#disease").val();
    if(disease.length>0){
    url=url+"?disease="+disease;
    $.get(url,function(data,status){
        var uid=eval(data);
        var checkboxs = $(".Protein").toArray();
        if(selectDiseases<1){
          for(var i=0;i<checkboxs.length;i++)
          {
            checkboxs[i].checked=false;
          }
          $(".tr_protein").hide();
        }
        for(var i=0;i<uid.length;++i){
           $("#tr_"+uid[i]).show();
           $("#checkbox_"+uid[i]).get(0).checked=true;
        }
        $("#All").get(0).checked=true;
    });
    }
  });
  
  $("#All").click(function(){
    var checkboxs=$(":checkbox").toArray();
    var allcheckboxs=$("#All").get(0);
    $(".tr_protein").show();
    for(var i=0;i<checkboxs.length;i++)
    {
      checkboxs[i].checked=allcheckboxs.checked;
    }
    if(allcheckboxs.checked){
      selectDiseases=diseasesCount;
    }
    else{
      selectDiseases=0;
    }
  });

  $(".disease").click(function(){
    var thisdisease=$(this).get(0);
    var disease=thisdisease.value;
    var checkboxs=$(".Protein").toArray();
    if(!thisdisease.checked){
       if(selectDiseases>1){
         var url="disease2uid.php";
         url=url+"?disease="+disease;
         $.get(url,function(data,status){
           var uid=eval(data);
           for(var i=0;i<uid.length;++i){
             $("#tr_"+uid[i]).hide();
             $("#checkbox_"+uid[i]).get(0).checked=false;
           }
         });
       }
       else{
         $(".tr_protein").show();
         for(var i=0;i<checkboxs.length;i++){
           checkboxs[i].checked=false;
         }
         $("#All").get(0).checked=false;
       }
       selectDiseases--;
    }
    else{
      var url="disease2uid.php";
      url=url+"?disease="+disease;
      $.get(url,function(data,status){
        var uid=eval(data);
        if(selectDiseases<2){
          for(var i=0;i<checkboxs.length;i++)
          {
            checkboxs[i].checked=false;
          }
          $(".tr_protein").hide();
        }
        for(var i=0;i<uid.length;++i){
           $("#tr_"+uid[i]).show();
           $("#checkbox_"+uid[i]).get(0).checked=true;
        }
        checkboxs=$(".Protein").toArray();
      });
      $("#All").get(0).checked=true;
      selectDiseases++;
    }
  });

});

</script>

<div class="content">

<div class="top-post">

<?php
if(isset($_SESSION['adminlogin'])){
	if($_SESSION['adminlogin']==1){
                echo '<form id="task_submit" action="php_upload_insert_db.php" method="post" enctype="multipart/form-data">
		<p><label for="task_title">Job Title:</label>
		<input name="task_title" type="text" id="task_title" value="title_'.date('ymdHis').'" size="40" maxlength="255" />
		</p>
		<p>&nbsp; </p>
		<p>
		  <label for="task_uploaded_file">Molecule File:</label>
		  <input type="file" name="task_uploaded_file" accept="/sdf,/mol"/> | <a href="./test.mol2" target="_blank">Sample File 1</a></p>
		  <p><small>File : The three-dimensional molecular structure file(mod2 file).</small></p>
		<p>&nbsp;</p>
		<p><input type="radio" name="command" value="SP" checked="checked" /> Glide SP
		&nbsp;
                <input type="radio" name="command" value="XP" /> Glide XP
                &nbsp;
		<input type="radio" name="command" value="vina" /> vina</p>
		<p>&nbsp;</p>
		<p>
		  <button type="submit">OK</button>
		</p>
		<p>&nbsp;</p>
		<p>Select Protein:</p>
                <p>&nbsp;</p>
                <p>';
                for($i=0;$i<count($diseases_variety);$i++)
                {
                   $disease=$redis->scard("diseases_".$diseases_variety[$i]);
                   echo '<input type="checkbox" class="disease" value="'.$diseases_variety[$i].'" />'.$diseases_variety[$i].'('.$disease.')&nbsp';
                }
                echo '</p>
                <p><input type="text" id="disease" /><input type="button" id="disease_select" value="select" /></p>
                <p id="result">&nbsp;</p>
		<table width="952" border="1" cellpadding="1" cellspacing="1" id="select_protein">
		  <tr>
			<th width="44"><input name="All" type="checkbox" id="All" value="" onClick="selectAllorNone(this,'."'Protein[]'".')"/></th>
			<th width="150">Target_Full_Name</th>
			<th width="181">Uniprot_ID</th>
			<th width="75">PDB_ID</th>
			<th width="372">diseases</th>
			</tr><tbody id="proteins">';

		$DDB_item=$redis->lrange('ddb_table',0,-1);
		for($i=0;$i<count($DDB_item);$i+=5){
			echo '<tr class="tr_protein" id="tr_'.$DDB_item[$i].'">';
			echo '<td><input name="Protein[]" class="Protein" id="checkbox_'.$DDB_item[$i].'" type="checkbox" value="' . $DDB_item[$i+3] . '" /></td>';
			echo "<td>" . $DDB_item[$i+1] . "</td>";
			echo '<td><a target="_blank" href="http://www.uniprot.org/uniprot/'. $DDB_item[$i+2] .'">' . $DDB_item[$i+2] . "</a></td>";
			echo "<td>" . $DDB_item[$i+3] . "</td>";
			echo "<td>" . $DDB_item[$i+4] . "</td>";
			echo "</tr>\n";
		}
                echo '</tbody>';
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
</table>
</form>
</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
