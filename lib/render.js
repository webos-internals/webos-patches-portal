var render = exports;
var fs = require('fs');
var _ = require("underscore");
var ejs=require("ejs")
var r = render.r = function(options, config, log)
 {
    this.options = options || {}
    this.config = config || null;
    this.log = log || null;
    this.cache = {};
}
r.prototype.render = function(str, obj, callback)
 {
    var self = this;
    this.parse(str, obj,
    function(p) {
        if (callback)
        callback(p || str || "",obj)
    })

}
r.prototype.renderFile = function(path, obj, callback)
 {
    var self = this;
    this.loadFile(path,
    function(data) {
        self.render(data, obj,
        function(page) {
            if (callback)
            callback(page || "",obj)
        })
    })
}
//Render a link either using already stored data, or building it from the data we are making now
r.prototype.renderLink=function(options,callback)
{
	if(options && options.name)
	this.config.get("links",options.name,function(l){
		options=options||{}
		var s="";
		s+="<a href='"
		s+=options.path||l.path
		s+="'>"
		s+=options.title||l.title
		s+="</a>";
		if(callback)
			callback(s,{"name":options.name||l.name,"path":options.path||l.path||"#","title":options.title||l.title||options.name||l.name});
	},{"name":options.name,"path":options.path||"#","title":options.title||options.name})
}
r.prototype.renderArray=function(arr,callback)
{
	var self=this;
	if(arr)
	{
		var keep=[];
		var counter=0;
		for(var i=0;i<arr.length;i++)
		{
			arr[i].obj._i=i
			if(arr[i].file)
			{
				this.renderFile(arr[i].file,arr[i].obj,function(entry,obj){
					keep[obj._i]=entry;
					counter++;
					if(counter==arr.length)
					{
						callback(keep.join(" "))
					}
				})
			}
			else if(arr[i].str)
			{
				this.render(arr[i].str,arr[i].obj,function(entry,obj){
					keep[obj._i]=entry;
					counter++;
					if(counter==arr.length)
					{
						callback(keep.join(" "))
					}
				})
			}
		}
	//	callback(" ")
	}
	else
	{
		callback(" ");
	}
}
r.prototype.renderEachFile=function(path,arr,callback)
{
	var self = this;
 	this.loadFile(path,function(file){
		self.renderEach(file,arr,function(entry){
			callback(entry)
		})
	});
}
r.prototype.renderEach=function(str,arr,callback)
{
	var self=this;
	var tmp=[]
	if(arr)
	{
		for(var i=0;i<arr.length;i++)
		{
			var o={"str":str,"obj":arr[i]}
			tmp.push(o)
		}
		this.renderArray(tmp,function(res){
			callback(res)
		})
	}
	else
	{
		callback(" ");
	}

}
r.prototype.parse = function(str, options, callback)
 {
	options=options||{};
	try{
		if (callback)
    callback(ejs.render(str, {"locals":{"local":options||{}}}));
	}catch(e)
	{
		if (callback)
    callback(str);
	}
	
 
}
r.prototype.loadFile = function(path, callback)
 {
    var self = this;
    try {
        var c = this.getCache(path)
        if(c)
        {
				self.log.debug("(Render) Loaded "+path+" from Cache");
        }
        else
        {
            this.config.get("server", "view",
            function(t) {
                self.config.get("server", "viewType",
                function(z) {
                    fs.realpath(t,
                    function(err, p) {
                        try {
                            fs.stat(p + "/" + path + z,
                            function(err, stat) {
                                fs.readFile(p + "/" + path + z, "utf8",
                                function(err, file) {
																		if(err)
																		self.log.error("(Render) "+err)
                                    self.log.debug("(Render) Read in " + p + "/" + path + z + " to render")
                                    callback(file);
                                })
                            })
                        } catch(e)
                        {
                            self.log.error("(Render) No Such File " + t + "/" + path + z + " to load")
                        }
                    })
                },
                ".js.html")
            },
            "./views")
        }
    } catch(e)
    {
        callback("")
    }

}
r.prototype.getCache = function(path, cb)
 {
	try{
		this.log.debug("(Render) Asking for "+path+" from cache");
	    if (cb)
	    cb(false)
	    else
	    return false
	}catch(e)
	{
	//	console.log(e)
	}

}