Feature: User
	In order to test the test system
	As a node script
	I want to be able see these tests succeed

	Background:
		Given I have placeholders
		
	Scenario: Placeholder Scenario Pass
		Given I have a placeholder scenario which will be "true"
		When I run this scenario
		Then the placeholder result should be "true" on the screen
		
	Scenario: Placeholder Scenario Fail
		Given I have a placeholder scenario which will be "false"
		When I run this scenario
		Then the placeholder result should be "false" on the screen