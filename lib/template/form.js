var formManager = exports;
var _ =require('underscore')
//The form needs a target, method, name, and optionally class and id and column number
var form=formManager.form=function(target,method,name,klass,id)
{
	this.target=target || "#";
	this.method=method || "POST";
	this.name=name || null;
	this.klass=klass || null;
	this.id=id || null
	this.contents=[];
	this.submitButton="Submit";
}
form.prototype.render=function(callback)
{
	var str="";
	str+="<form action=\""+this.target+"\" method=\""+this.method+"\" class=\"form "
	if(this.klass)
		str+=this.klass
	str+="\""
	if(this.id)
		str+=" id=\""+this.id+"\""
	str+=">"
	_.each(this.contents,function(entry){
		var estr="";
		estr+="<div class='group'>"
		if(entry.title)
			estr+="<label class='label'>"+entry.title+"</label>"
			estr+=entry.content;
		if(entry.label)
			estr+="<span class='description'>"+entry.label+"</span>"
		estr+="</div>"
		str+=estr
	})
	str+="<div class='group navform wat-cf'>"
	str+="<input type='submit' class='button' value='"+this.submitButton+"'>"
	str+="</div>"
	str+="</form>"
	if(callback)
		callback(str)
}
form.prototype.parse=function(params)
{
	
}
//Textbox needs name, title, label?
form.prototype.textbox=function(name,title,label)
{
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<input type='text' class='text_field' name='"+this.name+"["+name+"]'/>"
	this.contents.push(obj);
}
form.prototype.textarea=function(name,title,label)
{
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<textarea class='text_area' rows='10' cols='80' name='"+this.name+"["+name+"]'/></textarea>"
	this.contents.push(obj);
}
form.prototype.file=function(name,title,label)
{
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<input type='file' class='text_field' name='"+this.name+"["+name+"]'/>"
	this.contents.push(obj);
}
form.prototype.checkbox=function(name,title,options,label)
{
	
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<div>";
	var tname=this.name
	_.each(options,function(o){
		obj.content+="<div>"+o.title+": <input type='checkbox' name='"+tname+"["+name+"]["+o.value+"]' value='"+o.value+"'/></div>"
	});
	obj.content+="</div>"
	this.contents.push(obj);
}
form.prototype.submit=function(title)
{
	this.submit=title;
}
form.prototype.select=function(name,title,options,label)
{
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<div><select name='"+this.name+"["+name+"]' id='"+this.name+"["+name+"]'>";
	_.each(options,function(o){
		obj.content+="<option value='"+o.value+"'>"+o.title+"</option>"
	});
	obj.content+="</select></div>"
	this.contents.push(obj);
}