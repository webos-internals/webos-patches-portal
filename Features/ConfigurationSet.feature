Feature: set Configuration
	In order to set a configuration
	As a user
	I want to be able to enter the catalog, field, callback, and value
	
	Scenario: Successful Setting
		Given I have entered a catalog
		And I have an existing database
		And I have entered a value
		And I have entered a field
		And I have entered a callback
		When I press set
		Then the resulting screen should say true
		
