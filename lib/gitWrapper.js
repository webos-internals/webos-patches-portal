require('underscore');
var fs = require('fs');
var regexMaster=require('./regexMaster');
var regex=new regexMaster.master()
var ChildProcess = require('child_process');
var Path = require('path');
var utils = require('utils');
var gitWrapper = exports;
//Create  the regex objects used to parse the git logs.
regex.construct("commit <%[]commit%>").then("\\n").if("Author: <%[s]author%>").then("\\n").if("Date:\\s\\s\\s<%[s]date%>").then("\\n\\n").if("<%[n]message%>").then("\\n\\n").if(" <%[n]changes%>\\n$").call("home")
regex.construct("commit <%[]commit%>").then("\\n").if("Author: <%[s]author%>").then("\\n").if("Date:\\s\\s\\s<%[s]date%>").then("\\n\\n").if("<%[n]message%>").if("\\n\\n$").call("home")
regex.construct("commit <%[]commits%>").then("\\n").if("Merge: <%[]commitone%> <%[]committwo%>\\nAuthor: <%[s]author%>\\n").if("Date:\\s\\s\\s<%[s]date%>").then("\\n\\n").call("home");
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
    this.head = null;
    try {
        this.gitCommands = ["--git-dir=" + this.path + "/.git", "--work-tree=" + this.path];
    } catch(e) {
        this.gitCommands = ["--git-dir=" + this.path + "/.git"];
    }
    this.getRepo(this.path,
    function(err) {
        if (err)
        callback(err)
        else
        callback(false);
    })
}
//Return the currect HEAD sha
client.prototype.getHead = function(callback)
 {
    if (this.head)
    {
        var commit = this.head;
    }
    else
    {
        var commit = "";
        var head = "";
        var tmp = fs.readFileSync(this.path + "/.git/HEAD", "utf8");
        var match = tmp.match(/(.*)\/(.*)\n/);
        if (match && match[2])
        {
            head = match[2];
        }
        _.each(this.repo.branches,
        function(b) {
            if (b.branch == "master" && head == "")
            {
                commit = b.commits[0];

            }
            else if (b.branch == head)
            {
                commit = b.commits[0]
            }

        })
        this.head = commit;
    }
    if (callback)
    {
        callback(commit)
    }
    else
    {
        return commit;
    }

}
//Actually run the given git commands, appending the necessary information to it automatically
client.prototype.exec = function(command, callback)
 {

    var cmd = this.gitCommands.concat(command);
	
	console.log("git "+cmd.join(" "))
    var child = ChildProcess.spawn("git", cmd);
    var stdout = "";
    var stderr = "";
    child.stdout.setEncoding('binary');
    var go = true;
    var end = false;
    child.stdout.addListener('data',
    function(text) {
        stdout+=text;
    });
    child.stderr.addListener('data',
    function(text) {
        stderr+=text;
    });
    child.addListener('exit',
    function(code) {
        callback(stdout, stderr)

    });
    child.stdin.end();

}
//Check to make sure the given folder is an actual repository.  Should be renamed
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
//Return the log of a given version and path, or falls back to head and .
client.prototype.getLog=function(version, path, callback)
{
	version=version||this.getHead()
	path=path||"."
	var args = ["log","-z", "--summary", version, "--", path];
	this.exec(args,function(out,err){
		var commits=[];
		var entries=out.split("\0");
		//console.log(entries)
		_.each(entries,function(entry){
			var obj={};
			var tmp=regex.match(entry)
			if(tmp)
			{
			
			}
			else
			{
				console.log("No match for: ")
				console.log([entry])
			}
		});
	})
}
//Create a repo at the given path
client.prototype.makeRepo = function(path, options, callback)
 {

 }
//Return the tree at the given version and path, or head and ., and optionally runs recursively
client.prototype.getTree = function(version, path, callback, rec)
 {
    path = path || "";
    version = version || this.getHead();
    var self = this;
    this.exec(["ls-tree", version + ":" + path],
    function(out, err) {
        if (err && err.length > 2)
        {
            console.log(err)
        }
        else if (out && out.length >= 1)
        {
            var data = {};
            data.files = [];
            data.folders = [];
            var num = 0;

            var entries = out.split("\n");
            var num2 = entries.length - 1;
            _.each(entries,
            function(ent) {
                if (ent.length > 5) {
                    num++;
                    var tmp = ent.split(" ");
                    var obj = {
                        mode: tmp[0],
                        type: tmp[1]
                    };
                    if (tmp[2])
                    {
                        var tmp2 = tmp[2].split("\t");
                        obj.name = tmp2[1];
                        obj.sha = tmp2[0];
                    }
                    else
                    {
                    }
                    if (rec)
                    {
                        if (obj.type == "tree")
                        {
                            var dpath = "";
                            if (path.length > 2)
                            dpath = path + "/" + obj.name;
                            else
                            dpath = obj.name

                            self.getTree(version, dpath,
                            function(d) {
                                num2--;
                                obj.tree = d;
                                data.folders.push(obj);
                                if (num2 == 0)
                                callback(data)

                            })
                        }
                        else
                        {
                            num2--;
                            data.files.push(obj)
                            if (num2 == 0)
                            callback(data)
                        }
                    }
                
                else
                {
					if(obj.type=="tree")
						data.folders.push(obj);
					else if(obj.type=="blob")
						data.files.push(obj);

                }
}

            });
                    callback(data)

        }
    })
}
//Returns the branches of the repo, Optionally with all gathered info instead of just the branch name
client.prototype.getBranches = function(callback, all, sync)
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
//Return the tags of a repo, optionally with all gathered info instead of just the tag names
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
//Update in memory list of branches and tags
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