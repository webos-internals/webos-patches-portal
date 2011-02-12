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
