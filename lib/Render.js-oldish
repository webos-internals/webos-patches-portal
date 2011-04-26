var render = exports;
var fs = require('fs');
var _ = require("underscore");
var ejs=require("ejs")
var r = render.r = function(options, config, log)
 {
    this.options = options || {};
		this.path=this.options.path||"views"
    this.config = config || null;
    this.log = log || null;
    this.cache = {};
}
//This executes renderFile twice, once for layout, once for actual page.  And we do not require the full page name, just "login" or "index"
r.prototype.renderPage=function(path,options,callback,layout)
{
	var self=this;
	this.renderFile(this.p(path)+".js.html",options,function(file){
		options[path]=file;
		self.renderFile(self.p(layout||"layout")+".js.html",options,function(file){
			callback(file)
		})
	})
}
r.prototype.p=function(path,callback)
{
	var str=this.path+"/"+path;
	if(callback)
	callback(str);
	else
	return str;
}
r.prototype.renderFile=function(path,options,callback)
{
	var self=this;
	this.loadFile(path,function(file){
		if(file)
		{
			self.parse(file,options,function(data){
				console.log(data.toString())
				callback(data.toString())
			})
		}
		else
			callback("");
	})
}
r.prototype.render=function(file,options,callback)
{
	
}
r.prototype.parse = function(str, options, callback)
 {
	options=options||{};
	try{
		if (callback)
    callback(ejs.render(str,{}))
	}catch(e)
	{
		if (callback)
    callback(str);
	}
}
r.prototype.loadFile=function(path,callback)
{
	console.log(callback)
	var self=this;
	this.fileExists(path,function(yes){
		if(yes)
		{
			console.log(fs.realpathSync(path))
			fs.readFile(fs.realpathSync(path), function(err, data){
				if(err)
				callback(false);
				else
				callback(data);
			});
		}
		else
		{
			callback(yes);
		}
	})
}
r.prototype.fileExists=function(path,callback)
{
	try{
		console.log(fs.realpathSync(path))
		if(fs.statSync(path))
		{
			callback(true)
		}
	}catch(e)
	{callback(false);}
}