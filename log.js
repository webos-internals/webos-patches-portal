require("underscore")
exports["log"]=function(path){
console.log(path)
}
exports["logify"]=function(level,obj)
{
	//Parts of the string:
	var str="";
	if(typeof obj=="object")
	{
		
	}
	if(typeof obj=="string")
	{
		str+=obj;
	}
	console.log(level+" "+str);
}
function sql(str)
{
	return str;
}