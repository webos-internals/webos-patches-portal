Feature: Git
	In order to talk to git through a module
	As a node script
	I want to be able to use a wrapper script

	Background:
		Given I have a valid git repository
		
	Scenario: Pull From Git
		Given I have a valid git repo 
		When event
		Then outcome
	Scenario: Push To Git
		Given context
		When event
		Then outcome
	Scenario: Get Repository Status
		Given context
		When event
		Then outcome
	Scenario: Create A New Branch
		Given context
		When event
		Then outcome
	Scenario: Switch To A Different Branch
		Given context
		When event
		Then outcome
	Scenario: Remove An Existing Branch
		Given context
		When event
		Then outcome
	Scenario: Create A New Tag
		Given context
		When event
		Then outcome
	Scenario: Get A Tags Commit
		Given context
		When event
		Then outcome
	Scenario: Create A New Commit
		Given context
		When event
		Then outcome
	Scenario: Get An Existing Commit
		Given context
		When event
		Then outcome
	Scenario: Get A Commits Tree
		Given context
		When event
		Then outcome
