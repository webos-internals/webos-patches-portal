require.paths.unshift('./node_modules')
var cluster=require('cluster'), express = require('express'), http = require('http');
var db=require("./lib/Database");db=new db.database();
var Resource=require('express-resource');
var namespace=require('express-namespace');
var app = express.createServer();
app.use(express.cookieParser());
app.use(express.session({ secret: "keyboard cat" }));
app.resource('users', require('./controllers/User'));
app.get("/login",function(req,res){
	require("./controllers/User").loginGet(req,res)
})
app.post("/login",function(req,res){
	require("./controllers/User").loginPost(req,res)
})
app.get("/register",function(req,res){
	require("./controllers/User").registerGet(req,res)
})
app.post("/register",function(req,res){
	require("./controllers/User").registerPost(req,res)
})
app.get("/",function(req,res){
	require("./controllers/User").loginGet(req,res)
})
//cluster(app)
//  .set('workers', 1)
//	.use(cluster.pidfiles())
//  .use(cluster.debug())
	//.use(cluster.reload())
	//.use(cluster.stats({ connections: true, requests: true }))
//  .use(cluster.logger())
	//.use(cluster.cli())
	db.start(function(){
		app.listen(process.env.VMC_APP_PORT||3000);
	})
  
