var express=require("express");
var Log = require('coloured-log'); log = new Log(Log.DEBUG)
var patches=require("./lib/patch");patches = new patches.patch();
var app = express.createServer();
var config=require("./lib/config"); config=new config.config();
var template=require('./lib/template'); template= new template.t({},null,null,config,log);
var os=require('os');
app.configure(function(){
  //app.use(app.router);
  app.use(express.cookieDecoder());
  app.use(express.session());
  app.use(express.staticProvider(__dirname + '/public'));
});
//app.use(express.staticProvider(__dirname + '/public'));

app.get("/favicon.ico",function(req,res){
		
	res.send(404)
		
})
app.get("/",function(req,res){
	template.page(function(page){
		page.header(null,function(){
				page.userNav("Link!","#",true)
				page.userNav("Link!","#",false)
				page.footer(null,function(){
					page.sidebarBlock("Test Sidebar","This is a test sidebar thingie.  Weeee!",null,true);
					page.sidebarList("Test List",[{"title":"blah","class":"red","link":"#"}])
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
