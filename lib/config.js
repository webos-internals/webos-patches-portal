var database = exports;
require('underscore')
var fs=require('fs');
var config=database.config=function(options)
{
	this.options=options || {};
	this.data=this.options.path||"./data/";
	this.ext=this.options.ext || "jsdb";
	//this.
	this.cache={};
}
config.prototype.set=function(catalog,field,value,callback)
{
	if(!this.cache[catalog])
	this.cache[catalog]={};
	this.cache[catalog][field]=value;
	callback(true);
}
config.prototype.get=function(catalog,field,callback,def)
{
	def=def||null;
	var self=this;
	if(!this.cache[catalog])
		this.cache[catalog]={};
	if(this.cache[catalog][field])
		callback(this.cache[catalog][field]);
	else
	{
		//Load in the defaults from file;
		this.load(catalog,function(){
			if(self.cache[catalog][field])
				callback(self.cache[catalog][field])
			else
			{
				self.cache[catalog][field]=def;
				self.write(catalog,function(){
					callback(self.cache[catalog][field])
				})
			}
		});
	}

}
config.prototype.load=function(catalog,callback)
{
	try{
		//var in={};
		var self=this;
		fs.realpath(this.data,function(err,path){
			try{
				path+="/"+catalog+"."+self.ext
				if(err)
				console.log(err)
				fs.stat(path,function(err,stat){
					if(!err)
					fs.readFile(path,"utf8",function(err,data){
							try{
								self.cache[catalog]=JSON.parse(data);
							}catch(e)
							{
								self.cache[catalog]={}
							}
						
						callback(true)
					})
					else
					{
						callback(true)
					}
				});
			}
			catch(e)
			{
				console.log(e)
			}

		});

	}catch(e)
	{
		this.cache={};
		//console.log(e)
		callback(false)
	}
}
config.prototype.write=function(catalog,callback)
{
	try{
		var out=null;
		if(this.cache[catalog])
		{
			out=this.cache[catalog];
		}
		else
		{
			out={};
		}
		var self=this
		fs.realpath(this.data,function(err,path){
			path+="/"+catalog+"."+self.ext
			try{
				fs.stat(path,function(stat){
					fs.writeFile(path,JSON.stringify(out),"utf8",function(err){
						if(err)
						console.log(err)
						callback(true)
					})
				});
			}catch(e)
			{
				//console.log(e)
			}
		});
	}catch(e)
	{
	//	console.log(e)
	}
}