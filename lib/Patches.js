var master = exports;
var Sequelize = require("sequelize").Sequelize;
var sequelize = new Sequelize('patches', 'patches', '', {
  host: "silentbluesystems.com"
})
var patches = master.patches = function(options)
{
	options=options||{}
	this.users=options.user||null;
}
patches.prototype.find=function(query,callback)
{
	
}
patches.prototype.get=function(patch_id,callback)
{
	
}
patches.prototype.create=function(callback)
{
	
}
patches.prototype.new=function(patch,callback)
{
	
}
patches.prototype.update=function(patch,callback)
{
	
}
patches.prototype.remove=function(patch,callback)
{
	
}