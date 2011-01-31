var template = exports;
require('underscore')
var t=template.t=function()
{
	this.body=[];
	this.header="";
	this.footer="";
	this.title={};
	this.js=[];
	this.css=[];
	this.sidebar=[];
}
t.prototype.build=function()
{
	var str="<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'><html xmlns='http://www.w3.org/1999/xhtml' lang='en'>";
	console.log(str);
	return str;
}
t.prototype.header=function(title,nav,user)
{
	var str="";
	str+="<div id='header'>";
	str+="<h1><a href='"+title.url+"'>"+title.title+"</a></h1>";
	this.title=title;
	if(user && user.length>0)
	{
		str+="<div id='user-navigation'><ul class='wat-cf'>";
		_.each(user,function(navLink){
			str+="<li";
			if(navLink.class)
				str+=" class='"+navLink.class+"'";
			str+="><a href='"+navLink.url+"'>"+navLink.name+"</a></li>"
		});
		str+="</ul></div>";
	}
	if(nav && nav.length>0)
	{
		str+="<div id='main-navigation'><ul class='wat-cf'>";
		_.each(nav,function(navLink){
			str+="<li";
			if(navLink.class)
				str+=" class='"+navLink.class+"'";
			str+="><a href='"+navLink.url+"'>"+navLink.name+"</a></li>"
		});
		str+="</ul></div>";
	}
	str+="</div>"
	this.header=str;
	return str;
}