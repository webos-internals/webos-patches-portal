var fs=require('fs')
var _=require('underscore')
var master = exports;
var Sequelize = require("sequelize").Sequelize;
var sequelize = new Sequelize(JSON.parse(process.env["VCAP_SERVICES"])["mysql-5.1"][0].credentials.name, JSON.parse(process.env["VCAP_SERVICES"])["mysql-5.1"][0].credentials.user, JSON.parse(process.env["VCAP_SERVICES"])["mysql-5.1"][0].credentials.password, {
  host: JSON.parse(process.env["VCAP_SERVICES"])["mysql-5.1"][0].credentials.host,
	port: JSON.parse(process.env["VCAP_SERVICES"])["mysql-5.1"][0].credentials.port
})
var database = master.database = function(options)
{
	options=options||{}
	//Model Instantiation Here
	this.models=[];
	this.User = sequelize.define('User', {
	  username: Sequelize.STRING,
	  full_name: Sequelize.STRING,
		email: Sequelize.STRING,
		password: Sequelize.STRING,
		last_login_date: Sequelize.DATE,
		admin: Sequelize.BOOLEAN
	});
	this.Permission=sequelize.define("Permission",{
		permission: Sequelize.STRING,
		value: Sequelize.BOOLEAN
	})
	this.Role=sequelize.define("Role",{
		name: Sequelize.STRING
	})
	this.Patch=sequelize.define("Patch",{
		patch_id: Sequelize.STRING,
		name: Sequelize.STRING,
		category: Sequelize.STRING,
		icon: Sequelize.STRING
	})
	this.Revision=sequelize.define("Revision",{
		patch: Sequelize.TEXT
	})
	this.Device=sequelize.define("Device",{
		name: Sequelize.STRING,
		title: Sequelize.STRING,
		description: Sequelize.TEXT,
		icon: Sequelize.STRING,
		image: Sequelize.STRING,
		public: Sequelize.BOOLEAN
	})
	this.Version=sequelize.define("Version",{
		name: Sequelize.STRING,
		public: Sequelize.BOOLEAN,
		description: Sequelize.TEXT
	})
	this.Screenshot=sequelize.define("Screenshot",{
		link:Sequelize.STRING
	})
	this.File=sequelize.define("File",{
		path:Sequelize.STRING
	})
	this.models=["Patch","User","Permission","Role","Revision","File","Device","Version","Screenshot"]
	this.User.hasMany('permissions')//, this.Permission, 'users')
	this.Role.hasMany('permissions')//, this.Permission, 'roles')
	this.User.hasMany('roles')//, this.Role, 'users')
	this.Patch.hasMany('revisions')//, this.Revision, 'patches')
	this.Revision.hasMany("files")//,this.File,"revisions")
	this.Revision.hasMany("devices")//,this.Device,"revisions")
	this.Revision.hasMany("versions")//,this.Version,"revisions")
	this.Revision.hasMany("screenshots")//,this.Screenshot,"revisions")
}

database.prototype.load=function(callback)
{
	var self=this;
	sequelize.sync(function(tables,error){
		if(error)
		console.log(error)
			callback(self)
	})

}
