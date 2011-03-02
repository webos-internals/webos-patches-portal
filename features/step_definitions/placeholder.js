var Steps = require('cucumis').Steps;
var pl;
Steps.Given(/^I have placeholders$/, function (ctx) {
	ctx.done();
});

Steps.Given(/^I have a placeholder scenario which will be "([^"]*?)"$/, function (ctx, arg1) {
	pl=arg1
	ctx.done();
});

Steps.When(/^I run this scenario$/, function (ctx) {
	ctx.done();
});

Steps.Then(/^the placeholder result should be "([^"]*?)" on the screen$/, function (ctx, arg1) {
	pl.should.eql(arg1);
	ctx.done();
});

Steps.export(module);
