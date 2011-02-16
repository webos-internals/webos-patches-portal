var database = exports;
require('underscore')
var fs=require('fs');

var config=database.config=function(options)
{
	this.options=options || {};
	this.data=this.options.path||"./data/";
	this.ext=this.options.ext || "jsdb";
	//this.
	this.cache={};
}
config.prototype.get=function(cat,field,callback,def)
{
	this.load(cat,function(){})
	callback(def||"")
}
config.prototype.set=function(cat,field,value,callback)
{
	
}
config.prototype.load=function(cat,callback)
{
	try{
		var file=fs.readFileSync(this.data+cat+"."+this.ext,"utf8");
		console.log(file)
	}catch(e)
	{
		callback({})
	}
	

}
config.prototype.write=function(cat,callback)
{
	
}