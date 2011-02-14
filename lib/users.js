var users=exports;
var u=users.u=function(options,config,log)
{
	
}
u.prototype.can=function(uid,action,callback)
{
	//Right now set everything to active
	if(callback)
		callback(true)
}
