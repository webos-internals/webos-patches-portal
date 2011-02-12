require('underscore');
var Log = require('coloured-log')
,
log = new Log(Log.DEBUG)
var mongoose=require('mongoose').Mongoose;
try{
	//var db = mongoose.connect('mongodb://localhost/patches');
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