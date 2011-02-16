var search=exports;
var c=search.c=function(options,config,log,render,mongo)
{
	this.render=render
	this.mongo=mongo;
	this.options=options||{};
	this.log=log;
	this.config=config;
	this.data=[];
	this.types=[{"category":"single"}]
}
c.prototype.make=function(callback)
{
	callback(" ")
}
//Build returns an entry valid for the type with the;
c.prototype.build=function(type,selected,callback,ret)
{
	
}
c.prototype.single=function(name,title,options,callback,ret)
{
	
}
c.prototype.check=function(name,title,options,callback,ret)
{
	
}
c.prototype.getOptions=function(callback,ret)
{
	
}