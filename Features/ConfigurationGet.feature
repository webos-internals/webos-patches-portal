Feature: Get Configuration
	In order to get a configuration
	As a user
	I want to be able to enter the catalog, field, callback, and default
	
	Scenario: Successful Default Retrieval
		Given I have entered a catalog
		And I have an existing database
		And the requested field is not already in the database
		And I have entered a field
		And I have entered a callback
		And I have entered a default
		When I press get
		Then the resulting screen should say default
		
	Scenario: Successful Non-Default Retrieval
		Given I have not entered a default
		And I have an existing database
		And the requested field is not already in the database
		And I have entered a catalog
		And I have entered a field
		And I have entered a callback
		When I press register
		Then the resulting screen should say null
		
	Scenario: Missing Callback
		Given I have entered a catalog
		And I have an existing database
		And the requested field is not already in the database
		And I have not entered a callback function
		And I have entered a field
		When I press register
		Then there should be no result
		
