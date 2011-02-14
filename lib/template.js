var template = exports;
var pageMaster=require('./template/page')
var formMaster=require("./template/form");
var _ =require('underscore')
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
t.prototype.links=function(links,page,cb)
{
	callback(page)
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
	page.mainNav("New Patch","/patches/new",false)
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
t.prototype.patchNew=function(callback)
{
	var page=new pageMaster.page(this.options,this.patches,this.users,this.config,this.log);
	page.mainNav("Home","/",false)
	page.mainNav("Browse","#",false)
	page.mainNav("New Patch","/patches/new",true)
	page.mainNav("Update Patch","#",false)
	page.userNav("Login","#",false)
	page.userNav("Register","#",false)
	var form=new formMaster.form("#","POST","search");
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
			form.file("patch_screenshot","Patch Screenshot","Screenshot of the patch in action")
			form.select("patch_category","Patch Category",[{"title":"Mojo Modifications","value":"mojo"}],"The Category the Patch Belongs To")
			form.checkbox("patch_webos","Patch Compatible webOS Versions",[{"title":"1.4.6","value":"1.4.6"},{"title":"2.0.0","value":"2.0.0"},{"title":"2.0.1","value":"2.0.1"}],"Compatible webOS Versions for this patch")
			form.checkbox("patch_device","Patch Compatible Devices",[{"title":"Pre / Pre Plus","value":"pre"}],"Compatible Devices with this patch")
			form.textbox("patch_maintainer","Patch Maintainer","Main Maintainer of the patch.  Note: This WILL be public");
			
			form.textbox("patch_email","Maintainer Email","Maintainer Email Address")
			//form.textbox("patch_email_public","Maintainer Email Visibility","Check this to have your email address hidden from the public");
			form.textbox("patch_homepage","Patch Homepage","Homepage URL of the patch.  Making it the Precentral thread url or the Wiki page url are good choices.")
			form.render(function(f){
				page.contentBlock("New Patch",f,function(){
					page.make(function(str){
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