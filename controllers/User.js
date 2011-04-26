var render=require("../lib/Render.js");
var Database=require("../lib/Database");
var fs=require('fs');
var forms = require('forms'),
    fields = forms.fields,
    validators = forms.validators;
		var login_form = forms.create({
		    username: fields.string({required: true}),
		    password: fields.password({required: true}),
		    confirm:  fields.password({
		        required: true,
		        validators: [validators.matchField('password')]
		    })
		})
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
	render.renderPage(exports.loadPath("login"),{"render_location":"box"},function(file){res.send(file)})
}
exports.loginPost=function(req,res)
{
	render.renderPage(exports.loadPath("login"),{"render_location":"box","flash":[{"type":"error","message":"Not Authorized for this System"},{"type":"error","message":"Unable to Contact DB"}]},function(file){res.send(file)})
}
exports.registerGet=function(req,res)
{
	render.renderPage(exports.loadPath("register"),{"render_location":"box"},function(file){res.send(file)})
}
exports.registerPost=function(req,res)
{
	render.renderPage(exports.loadPath("register"),{"render_location":"box","flash":[{"type":"error","message":"Uh Oh!  We were unable to add you to the system!"}]},function(file){res.send(file)})
}