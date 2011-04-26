var render=require("../lib/Render.js");
var Database=require("../lib/Database");
var fs=require('fs');
exports.index = function(req, res){
	render.renderPage(exports.loadPath("index"),{},function(file){res.send(file)})
};
exports.new=function(req,res){
	render.renderPage(exports.loadPath("new"),{},function(file){res.send(file)})
}
exports.loadPath=function(method)
{
	return fs.realpathSync("views/users/"+method+".js.html");
}
exports.loginGet=function(req,res)
{
	render.renderPage(exports.loadPath("login"),{},function(file){res.send(file)})
}