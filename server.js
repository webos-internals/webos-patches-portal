var express=require("express");
var form = require('connect-form');
var Log = require('coloured-log'); log = new Log(Log.DEBUG)
var patches=require("./lib/patches");patches = new patches.patch();
var app = express.createServer(form({ keepExtensions: true }));
var config=require("./lib/config"); config=new config.config();
var users=require("./lib/users"); users=new users.u({},config,log);
var render=require("./lib/render"); render=new render.r({},config,log);
var template=require('./lib/template'); template= new template.t({},null,users,config,log,render);
var formMaster=require("./lib/template/form");
var os=require('os');
app.configure(function(){
  //app.use(app.router);
  app.use(express.cookieParser());
  app.use(express.session({"secret":"cheeseburger"}));
	
  app.use(express.static(__dirname + '/public'));
app.use(express.bodyParser());
});
app.get("/favicon.ico",function(req,res){	
	res.send(404)
})
app.get("/",function(req,res){
	console.log(req)
	template.index(req,res,function(page){
		res.send(page)
	})
});
app.get("/patches/new",function(req,res){
	template.patchNew(req,res,function(page){
		res.send(page)
	})
});
app.get("/login",function(req,res){
	template.login(req,res,function(page){
		res.send(page)
	})
});
app.get("/register",function(req,res){
	template.register(req,res,function(page){
		res.send(page)
	})
});
app.post("/register",function(req,res){
	template.registerPost(req,res,function(page){
		res.send(page)
	})
});
app.post("/login",function(req,res){
	//Check if the fields are There

	template.loginPost(req,res,function(page){
		res.send(page)
	})
	//console.log(res)
});
app.get(/(.*)/,function(req,res){
//	req.form.complete(function(err, fields, files){
		template.fourOhFour(req,res,function(page){
					res.send(page)
		})
//	});
});
/*app.get("/old",function(req,res){
	template.page(function(page){
		page.header(null,function(){
				page.userNav("Link!","#",true)
				page.mainNav("Link!","#",false)
				page.footer(null,function(){
					page.searchList("Category","category",[{"title":"Option Numero Uno","value":"uno"}])
					var form=new formMaster.form("#","POST","search");
					form.textbox("testy","Test Textbox","this is a test")
					form.textbox("testytwo","Test Textbox","this is a test")
					form.textarea("testyarea","Test TextArea","this is a test")
					form.file("testyfile","Test File","This is a test File Upload")
					form.checkbox("testycheck","Test Textbox",[{"title":"1.4.6","value":"1.4.6"}],"this is a test")
					form.select("testyselect","Test Textbox",[{"title":"1.4.6","value":"1.4.6"}],"this is a test")
					form.render(function(f){
						page.contentBlock("Test Form",f);
					})
					page.searchList("WebOS Version","webos",[{"title":"Option Numero Uno","value":"uno"}])
					page.searchCheck("Device","device",[{"title":"1.4.5","value":"1.4.5"},{"title":"1.4.6","value":"1.4.6"},{"title":"1.4.7","value":"1.4.7"}])
					page.search=true
					page.contentBlock("Test Block","This is a test block thingie.  It is made of unicorns and fairy dust.")
					page.sidebarBlock("Test Sidebar","This is a test sidebar thingie.  Weeee!",null,true);
					page.sidebarList("Test List",[{"title":"blah","class":"red","link":"#"}])
					page.contentList("Test List",[{"title":"blah","class":"red","content":"BLAH<br>jsiasd<br>jjasbhjs"},{"title":"blah","class":"red","content":"BLAH<br>jsiasd<br>jjasbhjs"}])
					page.sidebarBlock("Server Stats","Hostname: "+os.hostname()+"<br>OS: "+os.type()+" "+os.release()+"<br>Memory: "+(os.freemem()/1024/1024)+"MB/"+(os.totalmem()/1024/1024)+"MB<br>Load (1) Minute: "+os.loadavg()[0],null,true);
					//console.log(req)
					log.info(req.method+" "+req.socket.remoteAddress+" "+req.url)
					page.make(function(output){
						if(output)
							res.send(output)
						else
							res.send(201)
					})
				})

		})
	})
		
})*/

try{
	config.get("server","port",function(val){
		log.info("(Express) Starting server on port: "+val)
		app.listen(val)
	},"3000");
	
	
}catch(e)
{
	log.error("(Express) Could not start server: "+e)
}
