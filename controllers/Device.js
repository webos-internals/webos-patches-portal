var render=require("../lib/Render.js");
var Database=require("../lib/Database"); database= new Database.database();
var crypto = require('crypto');
var fs=require('fs');
var formidable = require('formidable');
exports.loadPath=function(method)
{
	return fs.realpathSync("views/devices/"+method+".js.html");
}
exports.controllers=function(callback)
{
	var controllers={};
	var contr=fs.readdir(fs.realpathSync("controllers"),function(err,l){
		var counter=0;
		for(var i=0;i<l.length;i++)
		{
			try{
				controllers[l[i].replace(".js","")]=require(fs.realpathSync("controllers/"+l[i]));
				counter++;
				if(counter==l.length)
				callback(controllers)
			}catch(e)
			{
				counter++;
				if(counter==l.length)
				callback(controllers);
			}
		}
	})
}
exports.index = function(req, res){
	exports.controllers(function(c){
		c.User.getUser(req,function(user){
			render.renderPage(exports.loadPath("index"),{},function(file){res.send(file)})
		})
	})
	
};
exports.getDevices=function(callback,public)
{
	database.load(function(db){
		db.Device.findAll(function(devices){
			callback(devices)
		})})
}
exports.new = function(req, res){
	//exports.getUser(req,function(usr){console.log(usr)})
	exports.getDevices()
	render.renderPage(exports.loadPath("new"),{},function(file){res.send(file)})
};
exports.create = function(req,res){
	var form = new formidable.IncomingForm();
	form.parse(req, function(err, fields, files) {
	});
}