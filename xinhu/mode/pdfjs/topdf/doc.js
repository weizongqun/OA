/**
*	创建：信呼开发团队(雨中磐石)
*	主页；http://xh829.com/
*	doc文件转pdf程序
*	使用：cscript //nologo doc.js //T:500 D:\a.doc
*	var fso 	= new ActiveXObject("Scripting.FileSystemObject"); 
*/
WScript.Echo("start...");
function getarr(caa){
	var s='';
	for(var a in caa)s+=' @@ '+a+'=>'+caa[a]+'';
	return s;
}

function gotourl(url){
	var xml =  new ActiveXObject("MSXML2.XMLHTTP");
	xml.open("GET",url,false);
	xml.send();
	WScript.Echo("goto:"+url+"");
}


var files 	= WScript.Arguments; 
var docfile = files(0); 
WScript.Echo(docfile);
var path  	= docfile.substr(0, docfile.lastIndexOf('.'));
var isok	= 0;
var Obj 	= new ActiveXObject("Word.Application");
var doc 	= Obj.Documents.Open(docfile);
var pdfpath = ""+path+".pdf";
var fso 	= new ActiveXObject("Scripting.FileSystemObject"); 
if(fso.FileExists(pdfpath))fso.DeleteFile(pdfpath);
fso = false;
try{
	doc.SaveAs(pdfpath,17, true);
	WScript.Echo("isuccess");
	isok	= 1;
}catch(e){
	WScript.Echo("Error:"+getarr(e)+"");
}
doc.Close();
Obj.Quit();
var yurl = files(1);
if(isok==1 && yurl)gotourl(yurl);
WScript.Quit(0);