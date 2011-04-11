var users=exports;
var mongoose=require('mongoose')
try{
	var db = mongoose.connect('mongodb://preyourmind.org/patchportal');
}catch(e)
{
	log.error("Mongo reports: "+e)
}
var u=users.u=function(options,config,log)
{
	this.sessions={};
	this.schema={};
	var Permissions = new mongoose.Schema({
	    title     : String
	  , value			: String
	});
	var User = new mongoose.Schema({
	    user    : mongoose.Schema.ObjectId
	  , username     : String
	  , email      : String
	  , password      : String
	  , permissions  : [Permissions]
		, regDate : Date
	});
	mongoose.model('User', User)
}
u.prototype.can=function(uid,action,callback)
{
	//Right now set everything to active
	if(callback)
		callback(true)
}
u.prototype.get=function(session,callback)
{
	callback(null);
}
u.prototype.login=function(username,password,session,callback)
{
	if(username && password && session)
	{
		callback(null)
	}
	else
	{
		callback(null);
	}
}
u.prototype.register=function(username,password,email,callback)
{
	if(username && email && password)
	{
		var obj={};
		if(username)
			obj.username=username;
		if(email)
			obj.email=email;
		this.exists(obj,function(res){
			if(!res)
				{
					var instance = new User();
					console.log(instance)
				}
		})	
	}
	else
	{
	callback(null)
	}
}
//obj is an object that may contain username or email
u.prototype.exists=function(obj,callback)
{
	callback(false);
}
