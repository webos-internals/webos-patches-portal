require('underscore');
var mongoose=require('mongoose').Mongoose;
var db = mongoose.connect('mongodb://localhost/patches');
mongoose.model('Patch', {

    properties: ['title', 'tag', 'contents', 'description','category','maintainer','homepage','changelog','admin_note'],

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
console.log(mongoose)
var patches = exports;
//Options mush include mongo and path info
var patch = patches.patch = function(options, callback)
 {
    }
//Find and return an array of patches.  Options can contain session information of a logged in user to authenticate the user for the actions as well. options.uid is restricted to this use only.
patch.prototype.find = function(patch, options, callback)
 {
    //Patch Format:
    /*
	Array of:
		id
		title
		tag
		contents
		description
		category
		screenshoturls[]
		webosversions[]
		maintainers[]
		homepage
		changelog
		note to admin
		status
			sudmit date
			accepted date
			denied date
			reason
		submitter
			name
			email
	Plus ID of latest version
	*/
}
//Syntactic Sugar Methods to this.find
//Return a single patch from the matched data, and if multiple patches match returns nothing.  Multiple versions return the latest version of the patch
patch.prototype.findOne = function(patch, options, callback)
 {
    callback(null, null)
}
//Only return a single latest patch that matches the patch options, or nothing
patch.prototype.findLatest = function(patch, options, callback)
 {

    }

//Adds a new patch, either a completely new patch or a new version
patch.prototype.add = function(patch, options, callback)
 {

    }
//Remove a patch for whatever reason.  Will no longer show up at all
patch.prototype.remove = function(id, options, callback)
 {

    }
//Approve a patch with an optional message
patch.prototype.approve = function(id, message, callback)
 {

    }
//Deny a patch with an optional message
patch.prototype.deny = function(id, message, callback)
 {

    }
//Returns the available categories, IE ones that have been gathered up
patch.prototype.getCategories = function(callback)
 {

    }
//Returns the available webos versions
patch.prototype.getWebosVersions = function(callback)
 {

    }
patch.prototype.userRegister = function()
 {

    }
patch.prototype.userLogin = function()
 {

    }
patch.prototype.userUpdate = function()
 {

    }
patch.prototype.userLogout = function()
 {

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