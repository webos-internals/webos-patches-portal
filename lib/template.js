var template = exports;
var pageMaster=require('./template/page')
var formMaster=require("./template/form");
var os=require('os')
var _ =require('underscore')
var content=require("./template/content")
//Template Takes a few paramenters: options, patches, users, and config
var t=template.t=function(options,patches,users,config,log,render)
{
	_.each({"options":options,"patches":patches,"users":users,"config":config,"render":render},function(val,key){
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
	this.r=render;
}
//Creates and returns in a callback a page object for construction
t.prototype.page=function(p,callback)
{

	//console.log(c)
	//type,options,patches,users,config,log,render,c
	callback(new pageMaster.page(p,"default",this.options,this.patches,this.users,this.config,this.log,this.r,new content.c(this.options,this.config,this.log,this.r)))
}
//Creates the header links if allowed
t.prototype.links=function()
{

}
t.prototype.fourOhFour=function(callback)
{
	this.page(function(page){
		page.mainNav("Home","/",true)
		page.mainNav("Browse","#",false)
		page.mainNav("New Patch","/patches/new",false)
		page.mainNav("Update Patch","#",false)
		page.userNav("Login","#",false)
		page.userNav("Register","#",false)
		page.header(null,function(){
			page.footer(null,function(){
				page.contentBlock("404 - Page Not Found","The Page you are looking for could not be found on this site.  Are you sure you entered the correct URL?",function(){
					page.make(function(str){
						callback(str||201)
					})
				})

			})

		});
		
		
	})
	//page.header()

}
t.prototype.mainNavDefault=function(page,callback)
{
	page.mainNav("index",function(){
		page.mainNav("browse_patch",function(){
			page.mainNav("new_patch",function(){
				page.mainNav("update_patch",function(){
					callback();
				})
			})
		})
	})

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
	var self=this;
	
	this.page("index",function(page){
	//	console.log(page)
	console.log("Index Loading")
			self.mainNavDefault(page,function(){
				console.log("Nav Loading")
					page.content.block("Test","Test Block",function(){
						console.log("Content Loading")
						page.make(function(p){
							console.log("Page Made")
							callback(p)
						})
				})
			})
	})
}
t.prototype.patchNew=function(req,res,callback)
{
	var self=this;
	var cl=new content.c(this.options,this.config,this.log,this.r);
	var page=new pageMaster.page("patch_new","default",this.options,this.patches,this.users,this.config,this.log,cl);
	page.mainNav("Home","/",false)
	page.mainNav("Browse","#",false)
	page.mainNav("New Patch","/patches/new",true)
	page.mainNav("Update Patch","#",false)
	page.userNav("Login","#",false)
	page.userNav("Register","#",false)
	var form=new formMaster.form("#","POST","new_patch");
	page.header(null,function(){
		page.footer(null,function(){
			//Fields Needed:
			/*
			Name/Title
			Description
			File
			Category //Will end up being created on the fly using data from the DB
			WebOS Versions	//Same as category
			Maintainer
			Screenshots //This one will be interesting to do... dynamically add a new field using client side JS?
			Email
			Email Private
			Homepage
			*/
			form.textbox("patch_name","Patch Name","Name of the patch.  Limit 45 Characters, alphanumeric only")
			form.textarea("patch_description","Patch Description","Description of the patch.")
			form.file("patch_file","Patch File","Patch File to upload")
			form.multiFile("patch_screenshot","Patch Screenshot","Screenshot of the patch in action")
			form.select("patch_category","Patch Category",[{"title":"Mojo Modifications","value":"mojo"}],"The Category the Patch Belongs To")
			form.checkbox("patch_webos","Patch Compatible webOS Versions",[{"title":"1.4.6","value":"1.4.6"},{"title":"2.0.0","value":"2.0.0"},{"title":"2.0.1","value":"2.0.1"}],"Compatible webOS Versions for this patch")
			form.checkbox("patch_device","Patch Compatible Devices",[{"title":"Pre / Pre Plus","value":"pre"}],"Compatible Devices with this patch")
			form.textbox("patch_maintainer","Patch Maintainer","Main Maintainer of the patch.  Note: This WILL be public");
			page.sidebarBlock("Server Stats","Hostname: "+os.hostname()+"<br>OS: "+os.type()+" "+os.release()+"<br>Memory: "+(os.freemem()/1024/1024)+"MB/"+(os.totalmem()/1024/1024)+"MB<br>Load (1) Minute: "+os.loadavg()[0],null,true);
			form.textbox("patch_email","Maintainer Email","Maintainer Email Address")
			form.boolean("patch_email_public","Maintainer Email Visibility","Hide the maintainer Email Address from the public Feeds.")
			//form.textbox("patch_email_public","Maintainer Email Visibility","Check this to have your email address hidden from the public");
			form.textbox("patch_homepage","Patch Homepage","Homepage URL of the patch.  Making it the Precentral thread url or the Wiki page url are good choices.")
			page.searchList("WebOS Version","webos",[{"title":"Option Numero Uno","value":"uno"}])
			page.searchCheck("Device","device",[{"title":"1.4.5","value":"1.4.5"},{"title":"1.4.6","value":"1.4.6"},{"title":"1.4.7","value":"1.4.7"},{"title":"1.4.8","value":"1.4.8"}])
			page.search=true
			form.render(function(f){
				page.contentBlock("New Patch",f,function(){
					page.make(function(str){
						self.log.info(req.method+" "+req.socket.remoteAddress+" "+req.url)
						callback(str)
					})
				})
			})

		})
	})
}
t.prototype.makePatchList=function(patches,page,callback)
{
	callback(page);
}