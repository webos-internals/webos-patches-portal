require('underscore');
var fs = require('fs');
var ChildProcess = require('child_process');
var Path = require('path');
var utils = require('utils');
var gitWrapper = exports;
/*
Git object format:
Obj
	Branch
		Commit
			Tags?
			Author
			committer
			commit (id)
			tree (id)
			parent (id)
			comment
			Files
				path
				
		Tree
	Tags
*/
var client = gitWrapper.client = function(options, callback)
 {
    this.path = options.path || "./modifications";
    this.options = options || {};
    this.repo = {};
    this.getRepo(this.path,
    function(err) {
        if (err)
        callback(err)
        else
        callback(false);
    })
}
client.prototype.getRepo = function(path, callback)
 {
    path = path || this.path;
    try {
        fs.statSync(path)
    } catch(e)
    {
        callback("Bad Repo Path: " + path + " " + e)
        callback(false);
        //return false;
    }
    try {
        fs.statSync(path + "/.git")
        callback(false);
    } catch(e)
    {
        callback("Not a Git Repo: " + path + "/.git " + e);
        //return false;
    }

}
client.prototype.makeRepo = function(path, options, callback)
{

}
client.prototype.getTree = function(version, path, callback)
{
	path= path || this.path;
	if(version && version.length >= 40)
	{
		
	}
	else
	{
		var obj=[];
		var dir=fs.readdirSync(path);
		_.each(dir,function(dir){
			var tmp=fs.statSync(path+"/"+dir);
			console.log(tmp)
			var data={};
			data.mode=parseInt(tmp.mode).toString(8);
			data.uid=tmp.uid;
			data.gid=tmp.gid;
			console.log(data)
		})
	}
}
client.prototype.getBranches = function(callback, all)
 {
    if (this.repo.branches)
    {
        var obj = [];
        _.each(this.repo.branches,
        function(branch) {
            if (all)
            obj.push(branch)
            else
            obj.push(branch.branch);
        });
        callback(obj, null)
    }
    else
    {
        this.updateBranchesTags(function(branches, tags, err) {
            if (branches && !err)
            {
                var obj = [];
                _.each(branches,
                function(branch) {
                    if (all)
                    obj.push(branch)
                    else
                    obj.push(branch.branch);
                });
                callback(obj, null)
            }
        });
    }
}
client.prototype.getTags = function(callback, all)
 {
    if (this.repo.tags)
    {
        var obj = [];
        _.each(this.repo.tags,
        function(branch) {
            if (all)
            obj.push(branch)
            else
            obj.push(branch.tag);
        });
        callback(obj, null)
    }
    else
    {
        this.updateBranchesTags(function(branches, tags, err) {
            if (tags && !err)
            {
                var obj = [];
                _.each(tags,
                function(branch) {
                    if (all)
                    obj.push(branch)
                    else
                    obj.push(branch.tag);
                });
                callback(obj, null)
            }
        });
    }
}
client.prototype.updateBranchesTags = function(callback)
 {
    var self = this;
    var obj = [];
    this.getRepo(null,
    function(err) {
        var file = fs.readFileSync(self.path + "/.git/packed-refs", 'utf8');
        var lines = file.split("\n");
        //Split each line into tags/branches
        _.each(lines,
        function(line) {
            if (line.length > 3)
            {
                var entries = line.split(" ");
                if (entries.length >= 2 && entries[0].length >= 5)
                {
                    var data = {};
                    data.commits = [];
                    data.commits.push(entries[0])
                    if (entries[1].match(/refs\/tags\/(.*)/))
                    {
                        data.type = "tag";
                        data.tag = entries[1].match(/refs\/tags\/(.*)/)[1];
                    }
                    else if (entries[1].match(/refs\/remotes\/origin\/(.*)/))
                    {
                        data.type = "branch"
                        data.branch = entries[1].match(/refs\/remotes\/origin\/(.*)/)[1]
                    }
                    else
                    {
                        }
                    obj.push(data);
                }
                else
                {
                    if (entries.length == 1)
                    {
                        if (entries[0][0] == "^")
                        {
                            var commit = entries[0].match(/\^(.*)/);
                            var data = obj.pop();
                            data.commits.push(commit[1]);
                            obj.push(data);
                        }
                    }
                }
            }
        });
        var res = {};
        res.branches = [];
        res.tags = [];
        _.each(obj,
        function(e) {
            if (e.type && e.type == "branch")
            {
                var tmp = {
                    "commits": e.commits,
                    "branch": e.branch
                };
                res.branches.push(tmp)
            }
            if (e.type && e.type == "tag")
            {
                var tmp = {
                    "commits": e.commits,
                    "tag": e.tag
                };
                res.tags.push(tmp)
            }
        })
        self.repo['branches'] = res.branches;
        self.repo['tags'] = res.tags;
        callback(res.branches, res.tags, false);
    });

}