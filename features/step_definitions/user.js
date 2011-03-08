var Steps = require('cucumis').Steps;

Steps.Given(/^I have a mongodb database$/, function (ctx) {
	ctx.pending();
});

Steps.Given(/^I have entered a username "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I have entered a password "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.When(/^I press user login$/, function (ctx) {
	ctx.pending();
});

Steps.Then(/^the result should be true on the screen$/, function (ctx) {
	ctx.pending();
});

Steps.Given(/^I have logged in a user with username "([^"]*?)" and password "([^"]*?)"$/, function (ctx, arg1, arg2) {
	ctx.pending();
});

Steps.Given(/^I have a valid unique session id "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I have no other user in memory with that username, password, or session id$/, function (ctx) {
	ctx.pending();
});

Steps.When(/^I press user watch$/, function (ctx) {
	ctx.pending();
});

Steps.Given(/^I have entered an email "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I have entered a password confirmation "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I do not have any users already with that username and email$/, function (ctx) {
	ctx.pending();
});

Steps.Given(/^the username, email, and password all successfully run through their own validation steps$/, function (ctx) {
	ctx.pending();
});

Steps.When(/^I press register user$/, function (ctx) {
	ctx.pending();
});

Steps.Given(/^I have a user with username "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I have logged him in using password "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I have added him to the active user list with session id "([^"]*?)"$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I have a permission "([^"]*?)" that I want to check$/, function (ctx, arg1) {
	ctx.pending();
});

Steps.Given(/^I know that I have access to that permission$/, function (ctx) {
	ctx.pending();
});

Steps.When(/^I press check user permission$/, function (ctx) {
	ctx.pending();
});

Steps.export(module);
