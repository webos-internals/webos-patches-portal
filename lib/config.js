var database=exports;
var fs=require("fs");
var config=database.config=function(path)
{
	this.catalogs={};
	this.path= path || "data/";
	this.overwrite= true;
}
config.prototype.get=function(catalog,item,callback,def)
{
	var res=this.getSync(catalog,item,def);
	callback(res)
}
config.prototype.getSync=function(catalog,item,def)
{
	if(this.catalogs[catalog] && this.catalogs[catalog][item])
	{
		return this.catalogs[catalog][item];
	}
	else
	{
		if(!this.catalogs[catalog])
			this.catalogs[catalog]={};
		var obj=this.readFile(catalog);
		if(obj[item])
			return obj[item]
		else
		{
			obj[item]=def;
			this.catalogs[catalog][item]=def
			this.writeFile(catalog,obj);
			return def;
		}

	}
}
config.prototype.setSync=function(catalog,item,value)
{
	try{
	if(!this.catalogs[catalog])
		this.catalogs[catalog]={};
		this.catalogs[catalog][item]=value;
		this.writeFile(catalog);
	}catch(e)
	{}
}
config.prototype.readFile=function(catalog)
{
	try {
		var data=fs.readFileSync(this.path+catalog+".dbson","utf8");
		var obj=JSON.parse(data);
		this.catalogs[catalog]=obj;
		return obj;
	}catch(e)
	{
		if(this.overwrite)
			this.writeFile(catalog)
		return {};
	}
}
config.prototype.writeFile=function(catalog,value)
{
	value=value || this.catalogs[catalog] || {};
	try{
		var data=JSON.stringify(value);
		fs.writeFileSync(this.path+catalog+".dbson",data,"utf8")
		
	}catch(e)
	{
		console.log("Could not write to db "+catalog+"!")
	}
}