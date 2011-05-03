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
	this.data={};
	this.constraint=[];
	this.submitButton="Submit";
}
//Valid is a function that gets three variables: value, this, and field. It must return an object with a message(or false where a default message is chosen) or true  callback is not required;
form.prototype.constrain=function(field,valid,callback)
{
	var obj={"field":field};
	obj.valid=valid;
	this.constraint.push(obj);
	if(callback)
		callback();
	else
		return true;
}
form.prototype.render=function(callback)
{
	var str="";
	str+="<form action=\""+this.target+"\" enctype='multipart/form-data' method=\""+this.method+"\" class=\"form "
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
form.prototype.parse=function(req,callback)
{
	if(req.form)
	{
		var self=this;
		req.form.complete(function(err,fields,files){
			_.each(fields,function(value,name){
				try{
					var m=name.match(/([^\]]*)\[([^\]]*)\]/)
					if(m && m[1]==self.klass);
					{
						self.data[m[2]]={"type":"field","value":value};
					}
				}catch(e)
				{}
			})
			_.each(files,function(value,name){
				try{
					var m=name.match(/([^\]]*)\[([^\]]*)\]/)
					if(m && m[1]==self.klass);
					{
						console.log(value)
					//	self.data[m[2]]={"type":"field","value":value};
					}
				}catch(e)
				{}
			})
			callback(self.data)
		});
		
		
	}
	else
	{
		if(callback)
		callback(this.data);
	}

}
form.prototype.error=function(message,callback)
{
	callback();
}
//Textbox needs name, title, label?
form.prototype.textbox=function(name,title,label,def)
{
	var obj={"name":name,"title":title,"label":label || ""};
	if(!this.data[name])
		this.data[name]={}
	def=this.data[name].value||def||""
	obj.content="<input type='text' class='text_field' name='"+this.name+"["+name+"]' value='"+def+"'/>"
	this.contents.push(obj);
}
form.prototype.password=function(name,title,label)
{
	if(!this.data[name])
		this.data[name]={}
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<input type='password' class='text_field' name='"+this.name+"["+name+"]'/>"
	this.contents.push(obj);
}
form.prototype.textarea=function(name,title,label,def)
{
	if(!this.data[name])
		this.data[name]={}
	def=this.data[name].value||def||""
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<textarea class='text_area' rows='10' cols='80' name='"+this.name+"["+name+"]'/></textarea>"
	this.contents.push(obj);
}
form.prototype.file=function(name,title,label,def)
{
	if(!this.data[name])
		this.data[name]={}
	def=this.data[name].value||def||""
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<input type='file' class='text_field' name='"+this.name+"["+name+"]'/>"
	this.contents.push(obj);
}
form.prototype.multiFile=function(name,title,label,num,def)
{
	num=num || 3
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="";
	for(var i=0;i<num;i++)
	{
		obj.content+="<input type='file' class='text_field' name='"+this.name+"["+name+"]["+i+"]'/>"
	}
	
	this.contents.push(obj);
}
form.prototype.boolean=function(name,title,label,def)
{
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<div>Yes: <input type='radio' name='"+this.name+"["+name+"]' value='true'/></div>"
	obj.content+="<div>No: <input type='radio' name='"+this.name+"["+name+"]' value='false'/></div>"
	this.contents.push(obj);
}
form.prototype.checkbox=function(name,title,options,label,def)
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
form.prototype.select=function(name,title,options,label,def)
{
	var obj={"name":name,"title":title,"label":label || ""};
	obj.content="<div><select name='"+this.name+"["+name+"]' id='"+this.name+"["+name+"]'>";
	_.each(options,function(o){
		obj.content+="<option value='"+o.value+"'>"+o.title+"</option>"
	});
	obj.content+="</select></div>"
	this.contents.push(obj);
}