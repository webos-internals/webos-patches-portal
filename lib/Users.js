var master = exports;
//var Sequelize = require("sequelize").Sequelize;
//var sequelize = new Sequelize('patches', 'patches', '', {
//  host: "silentbluesystems.com"
//})
//Options mush include mongo and path info
var users = master.users = function(options)
{
	options=options||{}
	this.patches=options.patches||null;
}
users.prototype.login=function(user,pass,callback)
{
	callback(false);
}
users.prototype.getSession=function(callback)
{
	
}
users.prototype.register=function(params,callback)
{
	
}
users.prototype.exists=function(params,callback)
{
	callback(false);
}
users.prototype.can=function(user,param,callback,otherwise)
{
	
}
users.prototype.updateProfile=function(user,params,callback)
{
	
}

//Controllerish Functions  Each function gets in res and req from the thingie, and in a callback _should_ return layout, page, and options for the page.
users.prototype.getLogin=function(res,req,callback)
{
	var obj={"layout":"layout","page":"login","options":{}}
	callback(obj);
}