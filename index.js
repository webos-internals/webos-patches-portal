var express=require('express');
//var contrib = require('express-contrib');
//var helpers = require('express-helpers');
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