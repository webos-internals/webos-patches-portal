require('underscore');
var Log = require('coloured-log')
,
log = new Log(Log.DEBUG)
var mongoose=require('mongoose')
try{
	var db = mongoose.connect('mongodb://preyourmind.org/patches');
}catch(e)
{
	log.error("Mongo reports: "+e)
}
/*
mongoose.model('Patch', {

    properties: ['title','name', 'tag', 'contents', 'description','category','maintainer','homepage','changelog','admin_note'],

    cast: {
    },

    indexes: ['title'],

    setters: {
    },

    getters: {
    },

    methods: {
    },

    static: {
        //findOldPeople: function(){
        //    return this.find({age: { '$gt': 70 }});
       // }
    }

});
*/
var patches = exports;
//Options mush include mongo and path info
var patch = patches.patch = function(options, callback)
 {
	//this.patchModel=db.model('Patch');
 }
//Find and return an array of patches.  Options can contain session information of a logged in user to authenticate the user for the actions as well. options.uid is restricted to this use only.
patch.prototype.find = function(patch, options, callback)
 {

}
//Syntactic Sugar Methods to this.find
//Return a single patch from the matched data, and if multiple patches match returns nothing.  Multiple versions return the latest version of the patch
patch.prototype.findOne = function(patch, options, callback)
 {
    callback(null, null)
}
//Returns up to num || 5 of the most recent patches
patch.prototype.recent=function(num,callback)
{
	var p=[];
	var obj={};
	obj.patch_id="crazy-test-patch-numero-uno"
	obj.patch_name="Test Patch"
	obj.patch_revision_id={"latest":"10","equal":[]};
	obj.patch_revisions=[{"tag":"10","equal":[],"date":""}]
	obj.patch_contents="IMMAPATCH"
	obj.patch_category="Test Category"
	callback(p)
	
}
//Test Placeholder function.  Callback gets passed result,message(if any)
patch.prototype.test = function(id, callback)
 {
    try {
        callback(true, null)
    } catch(e)
    {
        callback(true, null)
    }
}