var fs = require('fs');
var _ = require("underscore");
var ejs=require("ejs");
exports.renderPage=function(path,options,callback)
{
	try{
		options=options||{}
		exports.loadPath(path,function(file){
			exports.render(file,options,function(file){
				options.content=file;
				exports.loadPath(options.layout || "views/layout.js.html",function(layout){
					if(layout)
					{
						exports.render(layout,options,function(page){
							callback(page)
						})
					}
					else
					{
						callback(file)
					}
				})
			})
		})
	}
	catch(e)
	{
		callback(404);
		console.log(e)
	}

}
exports.render=function(file,options,callback)
{
	options=options||{};

	try{
		
		callback(ejs.render(file,{"locals":{"local":options}}))
	}catch(e)
	{
		console.log(e)
		callback(file)
	}
	
}
exports.FourOhFour=function(callback)
{
	
}
exports.loadPath=function(path,callback)
{
	try{
		var file=fs.readFile(path,function(err,data){
			if(err)
			console.log(err)
			callback(data.toString());
		})
		
	}catch(e)
	{
		console.log(e)
		callback("")
	}
	
}