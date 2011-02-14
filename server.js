var express=require("express");
var Log = require('coloured-log'); log = new Log(Log.DEBUG)
var patches=require("./lib/patches");patches = new patches.patch();
var app = express.createServer();
var config=require("./lib/config"); config=new config.config();
var template=require('./lib/template'); template= new template.t({},null,null,config,log);
var formMaster=require("./lib/template/form");
var os=require('os');
app.configure(function(){
  //app.use(app.router);
  app.use(express.cookieDecoder());
  app.use(express.session({"secret":"oilsucks"}));
  app.use(express.staticProvider(__dirname + '/public'));
});
//app.use(express.staticProvider(__dirname + '/public'));

app.get("/favicon.ico",function(req,res){
		
	res.send(404)
		
})
app.get("/",function(req,res){
	template.index(function(page){
		res.send(page)
	})
});
app.get("/old",function(req,res){
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
		
})
try{
	config.get("server","port",function(val){
		log.info("Starting Express Server on port "+val)
		app.listen(val)
	},"3000");
	
	
}catch(e)
{
	log.error("Could not start server due to error: "+e)
}
