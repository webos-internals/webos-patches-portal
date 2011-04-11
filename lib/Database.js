var fs=require('fs')
var _=require('underscore')
var master = exports;
//Options mush include mongo and path info
var database = master.database = function(options)
{
	options=options||{}
	this.connector=options.db || "sqlite"
	this.models={};

}
//This function loads in all of the straight Model files and then creates a workable model object for each.
database.prototype.load=function(callback)
{
	callback();
}
database.prototype.modelize=function(path,callback)
{
	callback()
}