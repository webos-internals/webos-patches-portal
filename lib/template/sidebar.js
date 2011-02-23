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
	var self=this;
	//We Render Flash, then Blocks/Lists
	if(this.data && this.data.length>0)
	{
		this.render.renderArray(this.data,function(page){
			callback(page)
		})
	}
	else
	{
		callback(" ");
	}

}
c.prototype.block=function(title,block,callback,notice)
{
	var obj={"title":title,"content":block};
	if(notice)
		obj.css="notice";
	this.data.push({"file":"sidebar/_block","obj":obj});
	callback();
}
c.prototype.list=function(title,items,callback)
{
	if(items && items.length>0)
	{
		this.render.renderEachFile("sidebar/_listEntry",items,function(list){
			self.data.push({"file":"sidebar/_list","obj":{"title":title,"list":list}})
			callback();
		})
	}
	else
	{
		callback();
	}

}
c.prototype.dynamicList=function(title,items,callback,type)
{
	callback();
}
