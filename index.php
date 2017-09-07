<?php  
	include "header.php";
?>
<div class="content">
  <div class="top-post">
    <p><strong></strong></p>
    <p></p>
<p>Welcome to PARA, a web server for predicting β2 adrenergic receptor agonist.</p><p>&nbsp;</p>
<p>β2 adrenergic receptor (β2 AR) agonist is a type of compounds that are forbidden to use as growth promoters in animal breeding. However, current specific analytical methods are powerless for the detection of diversity and new type structures of β2 AR agonists. Therefore, PARA, a free accessible web server, is designed to identify potential β2 adrenergic receptor agonists.
</p><p>&nbsp;</p>
<p>We provide a complete computational environment based on three approaches: structure-based molecular docking, ligand-based pharmacophore modeling and the three widely-used machine learning methods (support vector machine, k nearest neighbor and random forest). Instead of standalone models, an integrated classification model of β2 AR agonists can greatly increase the accuracy of prediction.
</p><p><img src="images/webserver.gif" width="800" height="571" alt="" /></p>
<p>To use PARA server, please submit a ligand file (sdf) in upload page, the server will run your job and return the results in several minutes. </p>
</div>
<div class="clear"></div>
</div>
</div>
<?php  
	include "footer.php";
?>
