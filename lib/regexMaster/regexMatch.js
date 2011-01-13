require('underscore');
var regex = exports;
var client=regex.client=function()
{
	this.entries=[];
	this.builtRegex=[];
	this.matchCount=0;
	this.phone=null;
	return this;
}
client.prototype.or=function(regex)
{
	return this;
}
client.prototype.if=function(regex)
{
	var m=this.toRegex(regex);
	this.makeRegex();
	this.entries.push([m]);
	return this;
}
client.prototype.makeRegex=function()
{
	var obj=[];
	_.each(this.entries,function(e){
		if(e.length==1)
		{
			if(obj.length>=1)
			{
				
			}
			else
			{
				var tmp={};
				tmp.regex=e[0].reg;
				tmp.fields=e[0].fields;
				obj.push(tmp)
			}
		}
	})
	console.log(obj)
}
client.prototype.match=function(regex)
{
	this.makeRegex()
}
client.prototype.each=function(regex)
{
	return this;
}
client.prototype.call=function(who)
{
	return this;
}
client.prototype.toRegex=function(str)
{
	var obj={};
	obj.orig=str;
	obj.options=[]
	obj.reg=str;
	obj.fields=[];
	obj.reg=str.replace(/<%\[([^\]]*)\]([^\s]*)%>/g,function(key,opt,val){
		var res="";
		var get=".";
		var count="*";
		for(var i=0;i<opt.length;i++)
		{
			obj.options.push(opt[i]);
		}
		if(opt.indexOf('s')!=-1)
		{
			o=".";
		}
		else
		{
			o="^\\s";
		}
		if(opt.indexOf("n")!=-1)
		{
			o="\\s\\S"
		}
		res="(["+o+"]"+count+")"
		obj.fields.push(val);
		return res;
	})
	return obj;
}