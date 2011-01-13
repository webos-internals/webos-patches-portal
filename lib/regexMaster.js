require('underscore');
var regexMatch=require('./regexMaster/regexMatch')
var regex = exports;
var master=regex.master=function()
{
	this.matches=[];
}
master.prototype.new=function(regex,cb)
{
	m=new regexMatch.client();
	if(cb)
	{
		m.if(regex).call(cb);
	}
	else
	{
		m.if(regex).call("home")
	}
	this.matches.push(m);
	return this;
	console.log(this.matches)
	
}
master.prototype.construct=function(regex)
{
	
}
master.prototype.match=function(str)
{
	_.each(this.matches,function(m){
		m.match(str)
	})
}
master.prototype.all=function()
{
	
}
master.prototype.remove=function(str)
{
	
}