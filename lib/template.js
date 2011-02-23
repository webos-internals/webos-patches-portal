var template = exports;
var pageMaster=require('./template/page')
var formMaster=require("./template/form");
var os=require('os')
var _ =require('underscore')
var content=require("./template/content")
var sidebar=require("./template/sidebar")
var search=require("./template/search")
//Template Takes a few paramenters: options, patches, users, and config
var t=template.t=function(options,patches,users,config,log,render)
{
	_.each({"options":options,"patches":patches,"users":users,"config":config,"render":render},function(val,key){
		if(!val)
		{
			good=false;
			log.error("(Template) Unable to load "+key+" module")
		}
	})
	var good=true;
	if(true)
	{
		log.info("(Template) Successfully Instatiated")
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
	//type,options,patches,users,config,log,render
	callback(new pageMaster.page(p,"default",this.options,this.patches,this.users,this.config,this.log,this.r,new content.c(this.options,this.config,this.log,this.r),new sidebar.c(this.options,this.config,this.log,this.r),new search.c(this.options,this.config,this.log,this.r,this.patches)))
}
t.prototype.register=function(req,res,callback)
{
	var self=this;
	this.page({"name":"register","path":"/register"},function(page){
		var form=new formMaster.form("#","POST","register");
		form.parse(req,function(){
			form.textbox("username","Username")
			form.textbox("email","Email")
			form.password("password","Password")
			form.password("password-confirmation","Password Confirmation")
			form.render(function(f){
					page.content.box("Register",f,function(){
						page.make(function(p){
							callback(p || " ")
						})
				})
			})
		});
		
	})
}
t.prototype.login=function(req,res,callback)
{
	var self=this;
	this.page({"name":"login","path":"/login"},function(page){
		var form=new formMaster.form("#","POST","login");
		form.parse(req,function(){
			form.textbox("username","Username")
			form.password("password","Password")
			form.render(function(f){
					page.content.box("Login",f,function(){
						page.make(function(p){
							callback(p || " ")
						})
				})
			})
		});
		
	})
}
t.prototype.redirect=function(req,res,callback)
{
	
}
t.prototype.loginPost=function(req,res,callback)
{
	var self=this;
	var form=new formMaster.form("#","POST","login");
	form.parse(req,function(data){
		if(data && data.username && data.password)
		{
			self.users.login(data.username,data.password,req.sessionID,function(user){
				if(user)
				{
					console.log(user)
				}
				else
				{
					self.login(req,res,function(p){
						callback(p)
					})
				}
			})
		}
		else
		{
			self.login(req,res,function(p){
				callback(p)
			})
		}
	});


}
//Creates the header links if allowed0
t.prototype.fourOhFour=function(req,res,callback)
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
	this.page({"path":req.url},function(page){
			self.mainNavDefault(page,function(){
					page.content.block("Page Not Found","The Requested Page could not be found.  RuhRoh!",function(){
						page.make(function(p){

							callback(p)
						})
				})
			},"halfhalo")
	})
}
t.prototype.mainNavDefault=function(page,callback,uid)
{
	var self=this;
	page.mainNav("index",function(){
		page.mainNav("browse_patch",function(){
			page.mainNav("new_patch",function(){
				page.mainNav("update_patch",function(){
					self.users.can(uid,"admin_view",function(yes){
						if(yes)
						{
							page.mainNav("admin",function(){
													callback();
							})
						}
						else
						{
												callback();
						}
					})

				})
			})
		})
	})

}
t.prototype.index=function(req,res,callback)
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
	
	this.page({"name":"index","path":"/"},function(page){
			self.mainNavDefault(page,function(){
					page.content.block("Test","Test Block",function(){
						page.content.list("Test List",[{"content":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>"},{"content":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>"}],function(){
							page.content.dynamicList("Test Dynamic Replacement List",[{"content":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>"},{"content":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>"}],function(){
								page.content.dynamicList("Test Dynamic Append List",[{"content":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>"},{"content":"<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>"}],function(){
								page.content.error("This is an error message",function(){
									page.content.warn("This is a warning message",function(){
										page.content.info("This is an informational message",function(){
											page.sidebar.block("Test Sidebar Block","This is a test Sidebar Block using the New Block Standard #101001",function(){
												page.sidebar.block("Test Sidebar Notice Block","This is a test Sidebar Notice Block using the New Block Standard #101001",function(){
													page.sidebar.list("Test Sidebar List",[],function(){
														page.sidebar.dynamicList("Test Dynamic List",[],function(){
															page.sidebar.dynamicList("Test Dynamic Replacement List",[],function(){
																page.users.get(req.sessionID,function(user){
																	page.sidebar.block("Session Info","Session ID: "+req.sessionID+"</br>User: "+user,function(){
																		var form=new formMaster.form("#","GET","test_post_form");
																		form.parse(req,function(){
																			form.textbox("patch_name","Patch Name","Name of the patch.  Limit 45 Characters, alphanumeric only")
																			form.render(function(f){
																				page.content.block("Test Form",f,function(){
																					page.make(function(p){
																						callback(p)
																					})
																				})
																			})
																		})

																},"notice")
																})
															},"replace")
														})
													})
			
											},"notice")
											})
										})
									})
								})
						})
						},"replace")
						})

				})
			},"halfhalo")
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