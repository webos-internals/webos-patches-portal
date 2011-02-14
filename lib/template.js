var template = exports;
var pageMaster=require('./template/page')
require('underscore')
//Template Takes a few paramenters: options, patches, users, and config
var t=template.t=function(options,patches,users,config,log)
{
	_.each({"options":options,"patches":patches,"users":users,"config":config},function(val,key){
		if(!val)
		{
			good=false;
			log.error("Template Module Unable to load "+key+" module")
		}
	})
	var good=true;
	if(true)
	{
		log.info("Template Module Successfully Loaded")
	}
	this.options=options || {};
	this.patches=patches || null;
	this.users=users || null;
	this.config=config|| null;
	this.log=log || null;
}
//Creates and returns in a callback a page object for construction
t.prototype.page=function(callback)
{
	callback(new pageMaster.page(this.options,this.patches,this.users,this.config,this.log))
}
t.prototype.index=function(callback)
{
	//Index page has User Links/Nav Links, List of the 5< most recent patches, and standard sidebar (search, git stats, recent patches (5), pending patches (5) (If Admin))
	/*
	Status:
	User Links 75% (Links are there, not tied to anything)
	Nav Links 75% (Links are there, not tied to anything)
	Main Recent Patches 0%
	Sidebar
		Search 0%
		Git Stats 0%
		Recent Patches 0%
		Pending Patches 0%
	*/
	var page=new pageMaster.page(this.options,this.patches,this.users,this.config,this.log);
	//page.header()
	page.mainNav("Home","/",true)
	page.mainNav("Browse","#",false)
	page.mainNav("New Patch","#",false)
	page.mainNav("Update Patch","#",false)
	page.userNav("Login","#",false)
	page.userNav("Register","#",false)
	page.header(null,function(){
		page.footer(null,function(){
			page.make(function(str){
				callback(str||201)
			})
		})

	});
	
	
}
t.prototype.makePatchList=function(patches,page,callback)
{
	callback(page);
}