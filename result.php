<?php  
	include "header.php";
    $jobtitle=$_GET['task_title'];
?>
<script src="jquery-1.11.3.min.js">
</script>
<script src="jquery-ui.min.js">
</script>
<script type="text/javascript">
$(document).ready(function(){
		// Get all the thumbnail
		$('div.pharmacophore').mouseenter(function(e) {

			// Calculate the position of the image tooltip
			x = e.pageX - $(this).offset().left;
			y = e.pageY - $(this).offset().top;

			// Set the z-index of the current item, 
			// make sure it's greater than the rest of thumbnail items
			// Set the position and display the image tooltip
			$(this).css('z-index','15')
			.children("div.tooltip")
			.css({'top': y + 10,'left': x + 20,'display':'block'});
			
		}).mousemove(function(e) {
			
			// Calculate the position of the image tooltip			
			x = e.pageX - $(this).offset().left;
			y = e.pageY - $(this).offset().top;
			
			// This line causes the tooltip will follow the mouse pointer
			$(this).children("div.tooltip").css({'top': y + 10,'left': x + 20});
			
		}).mouseleave(function() {
			
			// Reset the z-index and hide the image tooltip 
			$(this).css('z-index','1')
			.children("div.tooltip")
			.animate({"opacity": "hide"}, "fast");
		});

});
</script>
<style>
.pharmacophore { 
	/* position relative so that we can use position absolute for the tooltip */
	position: relative; 
	float: left;  
}
.tooltip { 
	/* by default, hide it */
	display: none; 
	/* allow us to move the tooltip */
	position: absolute; 
	/* align the image properly */
	padding: 8px 0 0 8px; 
}
</style>
<div class="content">

<div class="top-post">

<?php

echo "<p style='font-size:20px;'>Result:</p>";
echo "<p id='result'>&nbsp</p>";

echo '<div id="result-table">';
echo "<p style='font-size:15px;'>Machine learning:</p>";
$ml_error=$redis->get($jobtitle.'_ml_error');
if($ml_error>0){
	echo "<p>Machine learning is error.</p>";
}
else {
        $ml_summary=0;
	echo "<table border='1' cellpadding='1' cellspacing='1'>";
	echo "<tr>";
	echo '<th >&nbsp</th>';
	echo '<th width="80">SVM</th>';
	echo '<th width="80">Random Forest</th>';
	echo '<th width="80">KNN</th>';
	echo '<th width="80">classification</th>';
	echo "</tr>";
	echo "<tr>";
	echo "<th>result</th>";
	$svm_result=$redis->get($jobtitle.'_svm_result');
	$randomforest_result=$redis->get($jobtitle.'_RandomForest_result');
	$ibk_result=$redis->get($jobtitle.'_IBK_result');
	echo "<td>".($svm_result>0?"true":"false")."</th>";
	echo "<td>".($randomforest_result>0?"true":"false")."</th>";
	echo "<td>".($ibk_result>0?"true":"false")."</th>";
	$svm_prob=$redis->get($jobtitle.'_svm_probability');
	$randomforest_prob=$redis->get($jobtitle.'_RandomForest_probability');
	$ibk_prob=$redis->get($jobtitle.'_IBK_probability');
	if($svm_result>0)
		$svm_true_prob=$svm_prob;
	else
		$svm_true_prob=1-$svm_prob;
	if($randomforest_result>0)
		$randomforest_true_prob=$randomforest_prob;
	else
		$randomforest_true_prob=1-$randomforest_prob;
	if($ibk_result>0)
		$ibk_true_prob=$ibk_prob;
	else
		$ibk_true_prob=1-$ibk_prob;
	$summary_true_prob=($svm_true_prob+$randomforest_true_prob+$ibk_true_prob)/3;
	if($summary_true_prob<0.5){
		$summary_result="false";
		$summary_prob=1-$summary_true_prob;
	}
	else {
		$summary_result="true";
                $ml_summary++;
		$summary_prob=$summary_true_prob;
	}
	echo "<td>".$summary_result."</th>";	
	echo "</tr>";
	echo "<tr>";
	echo "<th>probability</th>";
	echo "<td>".number_format($svm_prob*100,1)."%</th>";
	echo "<td>".number_format($randomforest_prob*100,1)."%</th>";
	echo "<td>".number_format($ibk_prob*100,1)."%</th>";
	echo "<td>".number_format($summary_prob*100,1)."%</th>";
	echo "</tr>";
	echo "</table>";
}

echo "<p>&nbsp</p>";
echo "<p style='font-size:15px;'>Molecular Docking:</p>";
echo "<table border='1' cellpadding='1' cellspacing='1'>";
echo "<tr>";
echo '<th width="50">score</th>';
echo '<th width="50">reference</th>';
echo '<th width="50">classification</th>';
echo "</tr>";
echo "<tr>";
$glide_score=$redis->get($jobtitle.'_glide_score');
if(strlen($glide_score)<1)
    $glide_score=0;
echo '<td width="50">'.$glide_score.'</th>';
if($glide_score<-8.60){
	echo '<td width="50"> < -8.60</th>';
} elseif ($glide_score<-8.10){
	echo '<td width="50"> -8.1 ~ -8.60</th>';
} else {
	echo '<td width="50"> < -8.10</th>';
}
echo '<td width="50">';
if($glide_score<-8.60){
	echo 'true';
} elseif ($glide_score<-8.10){
	echo 'possible';
} else {
	echo 'false';
}
echo '</th>';
echo "</tr>";
echo "</table>";
if($glide_score<-8.10)
    $ml_summary++;

echo "<p>&nbsp</p>";
echo "<p style='font-size:15px;'>Pharmacophore:</p>";
echo "<table border='1' cellpadding='1' cellspacing='1'>";
echo "<tr>";
echo '<th width="50"><div class="pharmacophore">Pharmacophore1<div class="tooltip">
			<img src="images/pharmacophore1.gif" alt="" width="400" height="188" />
		</div></div></th>';
$AAADPR_8=$redis->get($jobtitle.'_AAADPR.8');
if(strlen($AAADPR_8)<1)
    $AAADPR_8=-1;
echo '<td width="50">'.($AAADPR_8>=0?"true":"false").'</td>';
echo "</tr>";
echo "<tr>";
echo '<th width="50"><div class="pharmacophore">Pharmacophore2<div class="tooltip">
			<img src="images/pharmacophore2.gif" alt="" width="400" height="210" />
		</div></div></th>';
$AADPR_32=$redis->get($jobtitle.'_AADPR.32');
if(strlen($AADPR_32)<1)
    $AADPR_32=-1;
echo '<td width="50">'.($AADPR_32>=0?"true":"false").'</td>';
echo "</tr>";
echo "<tr>";
echo '<th width="50"><div class="pharmacophore">Pharmacophore3<div class="tooltip">
			<img src="images/pharmacophore3.gif" alt="" width="400" height="194" />
		</div></div></th>';
$AADPR_229=$redis->get($jobtitle.'_AADPR.229');
if(strlen($AADPR_229)<1)
    $AADPR_229=-1;
echo '<td width="50">'.($AADPR_229>=0?"true":"false").'</td>';
echo "</tr>";
echo '<tr>';
echo '<th width="50"><div class="pharmacophore">Pharmacophore4<div class="tooltip">
			<img src="images/pharmacophore4.gif" alt="" width="400" height="188" />
		</div></div></th>';
$AADPRR_1667=$redis->get($jobtitle.'_AADPRR.1667');
if(strlen($AADPRR_1667)<1)
    $AADPRR_1667=-1;
echo '<td width="50">'.($AADPRR_1667>=0?"true":"false").'</td>';
echo "</tr>";
echo "<tr>";
echo '<th width="50">classification</th>';
$summary_phase=$AAADPR_8+$AADPR_32+$AADPR_229+$AADPRR_1667;
echo '<td width="50">'.($summary_phase>=0?"true":"false").'</td>';
echo "</tr>";
echo "</table>";
if($summary_phase>=0)
    $ml_summary++;
echo "<p>&nbsp</p>";
echo "<strong><p style='font-size:15px;'>Conclusion:</p>";
if($ml_summary>1)
    echo "<p>The ligand is an agonist.</p>";
else
    echo "<p>The ligand is not an agonist.</p>";
echo "</strong><p>&nbsp</p>";

echo "</div>";


echo "<div style='color:#999'><p style='font-size:20px;color:#000'>errorlog:";
echo "<p>&nbsp</p>";
$errorlog=$redis->get($jobtitle.'_errormessage');
if(strlen($errorlog)==0)
  $errorlog="<p>NULL</p>";
echo "<p style='color:#666'>".nl2br($errorlog)."</p>";
echo "</p></div>"; 

?>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
