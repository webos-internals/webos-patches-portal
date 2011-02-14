var pager = exports;
require('underscore')
var fs=require('fs')
var async=require('async');
//Template Takes a few paramenters: options, patches, users, and config
var page=pager.page=function(options,patches,users,config,log)
{
	/*
	The rendering system uses #{var} exclusively to build the page.

	
	*/
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
	this.title="";
	this.search=false;
	this.footerHolder="";
	this.userNavHolder=[];
	this.mainNavHolder=[];
	this.contentHolder=[];
	this.sidebarHolder=[];
	this.footerHolder={};
	this.searchHolder=[];
}
page.prototype.header=function(title,callback)
{
	
	var self=this;
	this.config.get("server","title",function(t){
		var res={};
		self.title=title || t;
		callback(true)
	},"Patches Portal")
	
}
page.prototype.footer=function(footer,callback)
{
	
	var self=this;
	this.config.get("server","footer",function(f){
		var res={};
		self.footerHolder=footer || f;
		callback(true)
	},"Copyright 2011 WebOS-Internals")
	
}
page.prototype.userNav=function(title,lk,active,callback,css)
{
	var self=this;
	var link={};
	link.css=css || ""
	if(active)
		link.css+=" active"
	link.title=title;
	link.link=lk
	this.userNavHolder.push(link);
	if(callback)
	callback(true)
	else
	return true;
}
page.prototype.mainNav=function(title,lk,active,callback,css)
{
	var self=this;
	var link={};
	link.css=css || ""
	if(this.mainNavHolder.length==0)
	{
		link.css+="first ";
	}
	if(active)
		link.css+="active "
	link.title=title;
	link.link=lk
	
	this.mainNavHolder.push(link);
	if(callback)
	callback(true)
	else
	return true;
}
page.prototype.search=function(res)
{
	res=res||true;
	this.search=res;
}
page.prototype.searchList=function(title,name,options,callback)
{
	var obj={"title":title,"name":name,"content":{"name":name,"list":""}};
	var self=this;
	/*
	_entry needs: Name, Title, Content
	_dropDownContainer needs: Name, List
	_dropdown needs: value?, title, selected?
	*/
		self.renderEachFile("search/_dropdown",options,function(search){
			obj.content.list+=search
			self.renderFile("search/_dropDownContainer",obj.content,function(entry){
				obj.content=entry;
				self.searchHolder.push(obj)
				if(callback)
				callback(true)
			})

		})

}
page.prototype.searchCheck=function(title,name,options,callback)
{
	var obj={"title":title,"name":name,"content":{"name":name,"list":""}};
	_.each(options,function(o){
		o.name=name;
	})
	var self=this;
	self.renderEachFile("search/_checkbox",options,function(list){
		obj.content.list+=list;
		self.renderFile("search/_checkBoxContainer",obj.content,function(entry){
			obj.content=entry;
			self.searchHolder.push(obj)
			if(callback)
			callback(true)
		})
	})
}
page.prototype.renderSearch=function(callback)
{
	var str="";
	var self=this;
	if(this.search==true)
	{
		this.renderEachFile("search/_entry",this.searchHolder,function(search){
			self.renderFile("search/_search",{"content":search},function(s){
				callback(s);
			})

		})
	}
	else
	{
		callback("")
	}
	
}
page.prototype.sidebarBlock=function(title,content,callback,notice)
{
	if(notice)
		notice="notice"
	var obj={"title":title || null,"content":content || "","notice":notice || null,"renderPath":"sidebar/_block"};
	this.sidebarHolder.push(obj)
	if(callback)
		callback(true);
	else
	return true;
}
page.prototype.sidebarList=function(title,entries,callback)
{
	var obj={"title":title || ""};
	var self=this
	this.renderEachFile("sidebar/_listEntry",entries,function(l){
		obj.list=l;
		obj.renderPath="sidebar/_list"
		self.sidebarHolder.push(obj)
		if(callback)
			callback(true)
	})

}
page.prototype.contentBlock=function(title,content,callback)
{
	var self=this;
	var obj={"title":title,"content":content,"renderPath":"content/_block"}
	this.contentHolder.push(obj);
	if(callback)
	callback(true)
}
page.prototype.contentList=function(title,entries,callback)
{
	var self=this;
	var obj={"title":title || ""};
	this.renderEachFile("content/_listEntry",entries,function(l){
		obj.list=l;
		obj.renderPath="content/_list"
		self.contentHolder.push(obj)
		if(callback)
			callback(true)
	})

}
page.prototype.flash=function(level,message,callback)
{
	if(callback)
	callback(true)
}
page.prototype.make=function(callback)
{
	var self=this;
	//Render Path
	/*
		User Links
		Nav Links
		Header
		Content
		Footer
		Sidebar
		Layout
	*/
	//Object to hold all the completed strings
	var obj={};
	obj.header={};
	this.renderEachFile("partials/_userNav",this.userNavHolder,function(uLink){
		self.renderEachFile("partials/_userNav",self.mainNavHolder,function(mLink){
			obj.header.userLinks=uLink;
			obj.header.mainLinks=mLink;
			obj.header.title=self.title;
			obj.footer=self.footerHolder;
			obj.sidebar="";
			//Render the SearchBar
			self.renderSearch(function(search){
						obj.sidebar+=search
						//Render the Sidebar Objects
						self.renderFileArray(self.sidebarHolder,function(sidebar){
							//Render the Page!!! Finally!
							obj.sidebar+=sidebar;
							self.renderFileArray(self.contentHolder,function(content){
								obj.content=content
								self.renderFile("layout",obj,function(pageString){
									callback(pageString)
								})
							});
						})
			})
	
		});
		
	});
	
}
//Similar to renderEachFile, but gets the filename from the array itself
page.prototype.renderFileArray=function(arr,callback)
{
	var str="";
	var counter=arr.length;
	var self=this;
	if(arr && arr.length>0)
	{
		_.each(arr,function(entry){
			self.renderFile(entry.renderPath,entry,function(ent){
				counter--;
				str+=ent
				if(counter==0)
					callback(str);
			});
		})
	}
	else
	{
		callback(" ")
	}
	
}
page.prototype.renderEach=function(str,arr,callback)
{
	var self=this;
	var res="";
	var counter=arr.length || 0;
	if(counter>0)
	{
		_.each(arr,function(entry){
			self.render(str,entry,function(s){
				res+=s;
				counter--;
				if(counter==0)
					callback(res);
			})
		});
	}
	else
	{
		callback("")
	}

}
page.prototype.renderEachFile=function(path,arr,callback)
{
	var self=this;
	if(arr && arr.length>0)
	{
		this.loadFile(path,function(file){
			self.renderEach(file,arr,function(str){
				callback(str);
			})
		});
	}
	else
	{
		callback(" ")
	}

}
page.prototype.renderFile=function(path,obj,callback)
{
	var self=this;
	this.loadFile(path,function(file){
		self.render(file,obj || {},function(str){
			callback(str);
		});
	})
}
page.prototype.render=function(str,obj,callback)
{
	str=str.replace(/#\{([^\}]*)\}/g,function(key,val){
		var p=val.split(".");
	
		if(p.length>1)
		{
				var rsp=obj;
			_.each(p,function(v){
				if(rsp && typeof rsp!="string "&& rsp[v] && typeof rsp[v]!="function")
				{
					rsp=rsp[v]
				}
				else
				{
					rsp=""
				}
			})
			return rsp;
		}
		else
		{
			if(obj[p])
			{
					return obj[p]
			}
			else
				return "";
		}

	});
	str=str.replace(/\$\{([^\}]*)\}([^\{]*)\{([^\}]*)\}\$/g,function(key,first,second,third){
		if(first==third)
		{
			var p=first.split(".");
			var rsp=obj;
			_.each(p,function(v){
				if(rsp && rsp[v])
				{
					rsp=rsp[v]
				}
				else
				{rsp=null}
			})
			if(rsp)
				return second;
			else
			return "";

		}
		else
		{
			return key; 
		}
	})
	callback(str);
}
page.prototype.loadFile=function(file,callback)
{
	if(this.config)
	{
		this.config.get("server","view",function(path){
			try{
				fs.realpath(path+"/"+file+".js.html",function(err,p){
					if(err)
					{
												console.log(err)
												callback("")
					}

						else
						{
							fs.readFile(p,"utf8",function(err,data){
								callback(data)
							});
						}

				})
			}catch(e){
			console.log(e)
			callback("");
			}
		},"./views")
	}
	else
	{
		log.error("Page Unable to use config to look up view path");
		callback("");
	}
}