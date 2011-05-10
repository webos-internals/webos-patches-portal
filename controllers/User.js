var render=require("../lib/Render.js");
var Database=require("../lib/Database"); database= new Database.database();
var crypto = require('crypto');
var fs=require('fs');
var formidable = require('formidable');
exports.index = function(req, res){
	exports.can("view_index",function(foo){
		console.log(foo)
	},req)
	render.renderPage(exports.loadPath("index"),{},function(file){res.send(file)})
};
exports.can=function(method,callback,req,id)
{
	try{
		if(req)
		{
			exports.getUser(req,function(user){
				user.getPermissions(function(perms){
					if(perms){}
					console.log(perms)
					callback(user.admin)
				})
			})
		}
		else
		{
			callback(false)
		}
	}catch(e){
		callback(false)
	}

}
exports.new=function(req,res){
	render.renderPage(exports.loadPath("new"),{},function(file){res.send(file)})
}
exports.loadPath=function(method)
{
	return fs.realpathSync("views/users/"+method+".js.html");
}
exports.loginGet=function(req,res)
{
	render.renderPage(exports.loadPath("login"),{"render_location":"box","flash":[{"type":"error","message":"You Do Not Have Permission to Access [REDACTED]"}],"header":{"title":"[REDACTED]"}},function(file){res.send(file)})
}
exports.loginPost=function(req,res)
{
	var form = new formidable.IncomingForm();
  var msg=[{"type":"error","message":"You Do Not Have Permission to Access [REDACTED]"}]
	form.parse(req, function(err, fields, files) {
    if(!fields.username || !fields.password)
		{
			msg.push({"type":"error","message":"Missing Login Parameters"})
			render.renderPage(exports.loadPath("login"),{"render_location":"box","flash":msg,"header":{"title":"[REDACTED]"}},function(file){res.send(file)})
		}
		else
		{
			database.load(function(db){
				db.User.find({"username":fields.username}, function(user) {
					if(user)
					{
						if(user.password==crypto.createHash('sha1').update("nothingtoseehere").update(fields.password).digest("Hex"))
						{
							exports.createSession(req,user.id,function(){
								console.log("Loggin in: "+user.username)
								res.redirect("/",303)
							})
							
						}
						else
						{
							msg.push({"type":"error","message":"Invalid Login Parameters"})
							render.renderPage(exports.loadPath("login"),{"render_location":"box","flash":msg,"header":{"title":"[REDACTED]"}},function(file){res.send(file)})
						}
					}
					else
					{
						msg.push({"type":"error","message":"Not a Valid User"})
						render.renderPage(exports.loadPath("login"),{"render_location":"box","flash":msg,"header":{"title":"[REDACTED]"},"login_form":fields},function(file){res.send(file)})
					}
				})
			});
			}
		})
}
exports.createSession=function(req,id,callback)
{
	var res=true;
	database.load(function(db){
		db.Session.find({"user_id":id},function(s){
			if(s)
			{
				s.session_id=req.sessionID;
				s.save(function(){
					callback(true)
				})
			}
			else
			{
				var u = new database.Session({"session_id":req.sessionID,"user_id":id});
				u.save(function(){
					callback(true);
				})
			}
		})
	})

}
exports.registerGet=function(req,res)
{
	render.renderPage(exports.loadPath("register"),{"render_location":"box"},function(file){res.send(file)})
}
exports.getUser=function(req,callback){
	try{
		if(req.sessionID)
		{
			database.load(function(db){
				db.Session.find({"session_id":req.sessionID},function(session){
					if(session && session['user_id'])
					{
						try{
							db.User.find(parseInt(session.user_id,10),function(user){
								callback(user)
							})
						}catch(e)
						{
							console.log(e.toString())
							callback(null)
						}
					}
					else
					{}
				})
			});
		}
		else
		{
			console.log("No Session!")
			callback(null);
		}
	}catch(e)
	{
		console.log(e.toString())
		callback(null)
	}

}
exports.registerPost=function(req,res)
{
	var form = new formidable.IncomingForm();
  var msg=[{"type":"error","message":"You Do Not Have Permission to Access [REDACTED]"}]
	form.parse(req, function(err, fields, files) {
		if(!fields.username)
		{
			msg.push({"type":"error","message":"Please Enter a Valid Username"})
		}
		if(!fields.email)
		{
			msg.push({"type":"error","message":"Please Enter a Valid Email"})
		}
		else
		{
			msg.push({"type":"warning","message":"Unable to check Email Validity"})
		}
		if(!fields.password || !fields.confirm || fields.password!=fields.confirm)
		{
				msg.push({"type":"error","message":"Please Enter a Valid Password "})
		}
		else
		{
			msg.push({"type":"warning","message":"Unable to check Password Validity"})
		}
		if(fields.username && fields.email && fields.password && fields.confirm)
		{
			database.load(function(db){
				db.User.find({"username":fields.username}, function(user) {
					if(user)
					{
						msg.push({"type":"error","message":"Username Already Exists"})
						render.renderPage(exports.loadPath("register"),{"render_location":"box","flash":msg},function(file){res.send(file)})
					}
					else
					{
						db.User.find({"email":fields.email}, function(email) {
							if(email)
							{
									msg.push({"type":"error","message":"Email Already Exists"})
									render.renderPage(exports.loadPath("register"),{"render_location":"box","flash":msg},function(file){res.send(file)})
							}
							else
							{
								var u = new database.User({
									username:fields.username,
									email:fields.email,
									password:crypto.createHash('sha1').update("nothingtoseehere").update(fields.password).digest("Hex"),
									real_name:fields.real_name
								})
								u.save(function(){
									req.session.flash=req.session.flash || [{"message":"Successfully Registered"}]
									res.redirect("/",303)
								})
							}
						})
					}
				})
			})
			
		}
		else
		{
			render.renderPage(exports.loadPath("register"),{"render_location":"box","flash":msg},function(file){res.send(file)})
		}
	})
	
}