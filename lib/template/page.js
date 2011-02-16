var pager = exports;
var _ =require('underscore')
var fs=require('fs')
var async=require('async')
//var async=require('async');
//Template Takes a few paramenters: options, patches, users, and config
/*
	Page Types:
	Default (Used for most/all pages, has header+links+footer+sidebar)
		Title
		UserLinks
		MainLinks
		Footer
		Content
		Sidebar
	
	
*/
var page=pager.page=function(name,type,options,patches,users,config,log,render,c)
{
	_.each({"options":options,"patches":patches,"users":users,"config":config},function(val,key){
		if(!val)
		{
			good=false;
			log.error("Page Module Unable to load "+key+" module")
		}
	})
	var good=true;
	if(true)
	{
		log.info("Page Module Successfully Loaded")
	}
	
	this.options=options || {};
	this.patches=patches || null;
	this.users=users || null;
	this.config=config|| null;
	this.log=log || null;
	this.type=type || "default"
	this.render=render || null
	this.js=[];
	this.css=[];
	//Instead of seperate objects for different parts of the page, have it all in one.
	this.data={"header":{}};
	this.nav={"main":[],"user":[]};
	//Content, Sidebar, and Search are now seperate 
	this.content=c || null;
	this.name=name || ""
	//this.sidebar=sidebar;
	//this.search=search
}
page.prototype.makeJS=function(callback)
{
	callback(" ")
}
page.prototype.make=function(callback)
{
  var self=this;
	switch(this.type)
	{
		case 'default':
		default:
			self.makeDefault(function(page){
				callback(page)
			})
			break;
	}
}
page.prototype.navLink=function(type,link,callback)
{
	//if(this.nav[type].length==0)
	this.nav[type].push(link);
	callback(true);
}
//We will load the info about the link from the filesystem based DB, and only will allow for path to be overridden
page.prototype.userNav=function(name,callback,path)
{
	var obj={"name":name,"path":path||""}
	if(path)
	obj.path=path
	this.navLink("user",obj,function(){
		callback();
	})
}
page.prototype.mainNav=function(name,callback,path)
{
	var obj={"name":name}
	if(path)
	obj.path=path
	this.navLink("main",obj,function(){
		callback();
	})
}

page.prototype.renderNavLinks=function(callback)
{
	var str="";
	var obj=this.nav;
	var self=this;
	this.renderNavLink(this.nav['user'],function(links){
		self.renderNavLink(self.nav['main'],function(l){

			callback({"main":l,"user":links})	
		})
	})
		
}
page.prototype.renderNavLink=function(obj,callback)
{
	var str=""
	var self=this;
	var counter=0;
	if(obj && obj.length>0)
	{
		_.each(obj,function(e){

			self.render.renderLink(e,function(l){
				var tmp={"link":l};
				tmp.css=""
				if(self.name==e.name)
				tmp.css+=" active"
				if(counter==0)
				tmp.css+=" first"
				self.render.renderFile("partials/_navLinks",tmp,function(l){
					str+=l;
					counter++;
					console.log(counter+"="+obj.length)
					if(counter==obj.length)
					{
									callback(str)
					}

				})
			})
		})
	}
	else
	{
		callback("")
	}

}
page.prototype.renderLayout=function(obj,callback)
{
	this.render.renderFile("layout",obj,function(page){
		callback(page||" ")
	})
}
page.prototype.makeDefault=function(callback)
{
	/*
		We Render in Order:
		CSS
		JS
		Title
		Nav Links
		Content
		Search
		Sidebar
		Footer
		Layout
	*/
	var self=this;
	var obj={"header":{}};
	this.renderNavLinks(function(links){
		console.log("Rendering Nav Links")
		links=links||{}
			obj.header.mainLinks=links.main || ""
			obj.header.userLinks=links.user || ""
			self.content.make(function(content){
				obj.content=content
				self.renderLayout(obj,function(p){
					callback(p)
				})
		
		})

	})

	this.render.renderLink({"name":"red"},function(l){
		
	})

}