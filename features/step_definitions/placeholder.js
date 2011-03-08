var Steps = require('cucumis').Steps;
Steps.Given(/^I have placeholders$/, function (ctx) {
	ctx.done();
});

Steps.Given(/^I have a placeholder scenario which will be true$/, function (ctx) {
	ctx.done();
});

Steps.When(/^I run this placeholder test$/, function (ctx) {
	ctx.done();
});

Steps.Then(/^the placeholder result should be true on the screen$/, function (ctx) {
	ctx.done();
});

Steps.Given(/^I have a placeholder scenario which will be false$/, function (ctx) {
	ctx.done();
});

Steps.Then(/^the placeholder result should be false on the screen$/, function (ctx) {
	ctx.done();
});

Steps.export(module);
