var render=require("../lib/Render.js");
var Database=require("../lib/Database"); database= new Database.database();
var crypto = require('crypto');
var fs=require('fs');
var formidable = require('formidable');
exports.loadPath=function(method)
{
	return fs.realpathSync("views/app/"+method+".js.html");
}
exports.fourOhFour = function(req, res){
	exports.getUser(req,function(usr){console.log(usr)})
	render.renderPage(exports.loadPath("404"),{},function(file){res.send(file)})
};
