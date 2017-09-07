function showAllResult(jobtitle,ligendname,uniprot){
    var url="getAllResult.php"
    url=url+"?jobtitle="+jobtitle
    url=url+"&ligendname="+ligendname
    url=url+"&uniprot="+uniprot
    url=url+"&sid="+Math.random()
    var elementid=ligendname+"-"+uniprot
    var newtbody="#tbody-"+elementid
    var newbutton="#button-"+elementid
    if($(newbutton).text()=="+"){
        $(newbutton).text("-");
        $(newtbody).load(url);
    }
    else
    {
         $(newbutton).text("+");
         $(newtbody).empty();
    }
}