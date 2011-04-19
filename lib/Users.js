var master = exports;
var Sequelize = require("sequelize").Sequelize;
var sequelize = new Sequelize('patches', 'patches', '', {
  host: "silentbluesystems.com"
})
//Options mush include mongo and path info
var users = master.users = function(options)
{
	options=options||{}
	this.patches=options.patches||null;
}
users.prototype.login=function(user,pass,callback)
{
	
}
users.prototype.register=function(params,callback)
{
	
}
users.prototype.exists=function(params,callback)
{
	
}
users.prototype.can=function(user,param,callback,otherwise)
{
	
}