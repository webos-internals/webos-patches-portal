var Steps = require('cucumis').Steps;
var config=require("../../lib/config");config=new config.config();
var catalog
var value;
var field;
var def;
Steps.Given(/^I have a configurator$/, function (ctx) {
	if(config)
	{
		catalog=null;
		value=null;
		field=null;
		def=null;
		ctx.done();
	}
	else
	{
		
	}
	
});

Steps.Given(/^I have entered "([^"]*?)" in the configurator as the catalog$/, function (ctx, arg1) {
	catalog=arg1;
	ctx.done();
});

Steps.Given(/^I have entered field "([^"]*?)" into the configurator$/, function (ctx, arg1) {
	field=arg1;
	ctx.done();
});

Steps.Given(/^I have entered value "([^"]*?)" into the configurator$/, function (ctx, arg1) {
	value=arg1;
	ctx.done();
});

Steps.When(/^I press set configuration$/, function (ctx) {
	ctx.pending();
});

Steps.Then(/^the result of the set should be true on the screen$/, function (ctx) {
	ctx.pending();
});

Steps.Given(/^I have entered "([^"]*?)" in the configuratior as the field$/, function (ctx, arg1) {
	field=arg1;
	ctx.done();
});

Steps.When(/^I press get configuration$/, function (ctx) {
	ctx.pending();
});

Steps.Then(/^the result of a get should be "([^"]*?)" on the screen$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I have entered "([^"]*?)" in the configuratior as the default value$/, function (ctx, arg1) {
	def=arg1;
	ctx.done();
});

Steps.Then(/^the result of a default get should be "([^"]*?)" on the screen$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Then(/^the result of the null get should be null on the screen$/, function (ctx) {
	ctx.pending();
});

Steps.export(module);
