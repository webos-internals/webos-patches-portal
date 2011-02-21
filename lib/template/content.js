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
//A List of items that contains at least content and also can have an icon, class, id, onclick
c.prototype.list=function(title,items,callback)
{
	var self=this;
	this.render.renderEachFile("content/_listEntry",items,function(list){
		self.data.push({"file":"content/_list","obj":{"title":title,"list":list}})
		callback();
	})
	
}
c.prototype.box=function(title,content,callback)
{
	callback();
}
//A list setup to either automatically switch between two divs, or show both at once
c.prototype.dynamicList=function(title,items,callback,type)
{
	var keep=[];
	type=type||"append"; //Could also be addition
	for(var i=0;i<items.length;i++)
	{
		if(items[i].class)
		var t=items[i].class;
		else
		var t=""
		items[i].class="dynamicList-"+type+" "+t;
		items[i].content="<div class='dynamicList-simple'>"+items[i].content+"</div><div class='dynamicList-complex' style='display:none'>"+(items[i].complex||items[i].content+" "+items[i].content)+"</div>";
	}
	this.list(title,items,function(){
		callback();
	})
	
}
c.prototype.error=function(message,callback)
{
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