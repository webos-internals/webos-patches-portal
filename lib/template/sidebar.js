var sidebar=exports;
var c=sidebar.c=function(options,config,log,render)
{
	this.render=render
	this.options=options||{};
	this.log=log;
	this.config=config;
	this.data=[];
}
c.prototype.make=function(callback)
{
	callback(" ")
}
c.prototype.block=function(title,block,notice,callback)
{
	var obj={"title":title,"content":block};
	this.data.push(obj);
	callback();
}
c.prototype.list=function(title,items,callback)
{
	callback();
}
