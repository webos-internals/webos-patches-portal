require.paths.unshift('./node_modules')
var cluster=require('cluster'), express = require('express'), http = require('http');
var render=require('./lib/Render');render=new render.r();

var database=require('./lib/Database');database=new database.database({});
var app = express.createServer();
database.load(function(){
	
})
var users=require("./lib/Users");users=new users.users({});
app.get('/', function(req, res){
	users.getLogin(req,res,function(obj){
		render.renderPage(obj.page,obj.options,function(file){
			res.send(file);
		},obj.layout)
	})
});

//cluster(app)
//  .set('workers', 1)
//	.use(cluster.pidfiles())
//  .use(cluster.debug())
	//.use(cluster.reload())
	//.use(cluster.stats({ connections: true, requests: true }))
//  .use(cluster.logger())
	//.use(cluster.cli())
  app.listen(process.env.VMC_APP_PORT||3000);