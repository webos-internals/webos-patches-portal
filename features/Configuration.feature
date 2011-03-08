Feature: Configuration
	In order to have all config data in one place
	As a node script
	I want to be able to get and set data easily
	Scenario: Set A Value
		Given I have a configurator
		And I have entered "test" in the configurator as the catalog
		And I have entered field "color" into the configurator 
		And I have entered value "red" into the configurator 
		When I press set configuration
		Then the result of the set should be true on the screen
	Scenario: Get A Value
		Given I have a configurator
		And I have entered "test" in the configurator as the catalog
		And I have entered "color" in the configuratior as the field 
		When I press get configuration
		Then the result of a get should be "red" on the screen
	Scenario: Get A Default Value
		Given I have a configurator
		And I have entered "test" in the configurator as the catalog
		And I have entered "size" in the configuratior as the field
		And I have entered "42" in the configuratior as the default value
		When I press get configuration
		Then the result of a default get should be "42" on the screen
	Scenario: Get A Non Existant Value
		Given I have a configurator
		And I have entered "test" in the configurator as the catalog
		And I have entered "shape" in the configuratior as the field
		When I press get configuration
		Then the result of the null get should be null on the screen
		