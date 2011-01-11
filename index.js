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
		//console.log("Success!")

	}
});
gitWrapper.getBranches(function(branches,err){
	console.log(branches)
})