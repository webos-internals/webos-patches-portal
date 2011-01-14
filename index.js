var express=require('express');
var Log = require('coloured-log')
,
log = new Log(Log.DEBUG)
var ejs=require('ejs')
var fs=require('fs')
var patches=require("./lib/patch");
var patch=new patches.patch()
var app = express.createServer();
app.use(express.cookieDecoder());
app.use(express.session());
app.get('/', function(req, res){
	//console.log(req)
	log.info("Request From: "+req.headers.host+" -- "+req.method+" "+req.url+"\n UA: "+req.headers['user-agent'])
    res.send(render('index'));
});

app.listen(3000);
//var contrib = require('express-contrib');
//var helpers = require('express-helpers');
function render(path)
{
	var str="";
	var layout=fs.readFileSync("./templates/application.layout.html","utf8");
	str+=layout;
	return str;
}
var wrapper= require('./lib/gitWrapper');
var gitWrapper=new wrapper.client({"path":"/Users/halfhalo/src/modifications"},function(err){
	if(err)
	{
		console.log(err)
	}
	else
	{
	}
});
patch.find()
gitWrapper.getBranches(function(branches,err){
	//console.log(branches)
},true)
gitWrapper.getTags(function(tags,err){
	//console.log(tags)
})
gitWrapper.getTree(null,null,function(tags,err){
	//console.log(tags)
})
gitWrapper.getLog(null,null,function(log){
	console.log(log)
})