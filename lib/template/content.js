var content=exports;
var c=content.c=function(options,config,log,render)
{
	this.render=render
	this.options=options||{};
	this.log=log;
	this.config=config;
	this.data=[];
	this.flashMessages=[];
}
c.prototype.make=function(callback)
{
	var self=this;
	//We Render Flash, then Blocks/Lists
	this.render.renderArray(this.data,function(page){
		callback(page)
	})
	
}
c.prototype.block=function(title,block,callback)
{
	var obj={"title":title,"content":block};
	this.data.push({"file":"content/_block","obj":obj});
	callback();
}
c.prototype.list=function(title,items,callback)
{

}
c.prototype.error=function(message,callback)
{
	console.log(message)
	this.flash("error",message,function(){
		if(callback)
		callback();
	})	
}
c.prototype.warn=function(message,callback)
{
	this.flash("warn",message,function(){
		callback();
	})	
}
c.prototype.flash=function(type,message,callback)
{
	callback();
}
c.prototype.info=function(message,callback)
{
	this.flash("info",message,function(){
		if(callback)
		callback();
	})
}