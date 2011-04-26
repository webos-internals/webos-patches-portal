require.paths.unshift('./node_modules')
var cluster=require('cluster'), express = require('express'), http = require('http');
var Resource=require('express-resource');
var namespace=require('express-namespace');
var app = express.createServer();
app.use(express.cookieParser());
app.use(express.session({ secret: "keyboard cat" }));
app.resource('users', require('./controllers/User'));
app.get("/login",function(req,res){
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
  app.listen(process.env.VMC_APP_PORT||3000);