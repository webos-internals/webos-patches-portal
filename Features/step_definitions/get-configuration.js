/*
 * Get Configuration.feature
 * Step definitions for Feature: 'Get Configuration'
 *
 * Auto-generated using Kyuri: http://github.com/nodejitsu/kyuri
 */
 
var kyuri = require('kyuri'),
    Steps = require('kyuri').Steps;

//
// Step definitions for Scenario: Successful Default Retrieval
//
Steps.Given(/^GIVEN I have entered a catalog$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your Given code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have an existing database$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND the requested field is not already in the database$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have entered a field$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have entered a callback$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have entered a default$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.When(/^WHEN I press get$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your When code here. */
    
    return topic;
  };
});

Steps.Then(/^THEN the resulting screen should say default$/, function (topic) {
  return function () {
    /* Put your assert messages for this Then here */
  };
});

//
// Step definitions for Scenario: Successful Non-Default Retrieval
//
Steps.Given(/^GIVEN I have not entered a default$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your Given code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have an existing database$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND the requested field is not already in the database$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have entered a catalog$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have entered a field$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have entered a callback$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.When(/^WHEN I press register$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your When code here. */
    
    return topic;
  };
});

Steps.Then(/^THEN the resulting screen should say null$/, function (topic) {
  return function () {
    /* Put your assert messages for this Then here */
  };
});

//
// Step definitions for Scenario: Missing Callback
//
Steps.Given(/^GIVEN I have entered a catalog$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your Given code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have an existing database$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND the requested field is not already in the database$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have not entered a callback function$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.And(/^AND I have entered a field$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your And code here. */
    
    return topic;
  };
});

Steps.When(/^WHEN I press register$/, function (topic) {
  return function () {
    // Always use or extend the same topic since you don't 
    // know how nested or not nested you are at this point
    topic = topic || {};
    
    /* Put your When code here. */
    
    return topic;
  };
});

Steps.Then(/^THEN there should be no result$/, function (topic) {
  return function () {
    /* Put your assert messages for this Then here */
  };
});

Steps.export(module);