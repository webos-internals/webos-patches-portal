var express=require("express");
var Log = require('coloured-log')
var log = new Log(Log.DEBUG)
var jade=require('jade')
var fs=require('fs')
var patches=require("./lib/patch");
var patch=new patches.patch();
var app = express.createServer();
//app.use(express.cookieDecoder());
//app.use(express.session());
app.set('views', __dirname + '/views');
app.set('view engine', 'jade');
app.use(express.staticProvider(__dirname + '/public'));
app.get("/",function(req,res){
		res.render("index",{locals:{nav:"",pageTitle:"Test Page",userBox:""}})
})
log.info("Starting Express Server on port 3000")
app.listen(3000)