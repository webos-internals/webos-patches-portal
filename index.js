var cluster=require('cluster'), express = require('express'), http = require('http');
var database=require('./lib/Database');database=new database.database({});
var app = express.createServer();
database.load(function(){
	
})
app.get('/', function(req, res){
  res.send('Hello World');
});

cluster(app)
  .set('workers', 1)
	.use(cluster.pidfiles())
  .use(cluster.debug())
	//.use(cluster.reload())
	//.use(cluster.stats({ connections: true, requests: true }))
  .use(cluster.logger())
	//.use(cluster.cli())
  .listen(3000);