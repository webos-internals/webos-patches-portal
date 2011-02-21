var users=exports;
var mongoose=require('mongoose')
try{
	var db = mongoose.connect('mongodb://preyourmind.org/users');
}catch(e)
{
	log.error("Mongo reports: "+e)
}
var u=users.u=function(options,config,log)
{
	
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
