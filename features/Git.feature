Feature: Git
	In order to talk to git through a module
	As a node script
	I want to be able to use a wrapper script

	Background:
		Given I have a valid git repository
		
	Scenario: Pull From Git
		Given I have a valid git repo 
		And I have a remote origin point set
		When I want to pull from a remote repo
		Then I should recieve git pull statistics
	Scenario: Push To Remote Git With Permissions
		Given I have a valid git repo
		And I have a remote origin point set
		And I have the ability to push to the remote repo
		And I have commits to push
		When I push to a remote repo
		Then I should recieve git push confirmation
	Scenario: Get Repository Status
		Given I have a valid git repo
		And I have a remote origin point set
		When I ask for the status of the repo
		Then I should get repo statistics
	Scenario: Create A New Branch
		Given I have a valid git repo
		And I have a valid branch name
		And I do not have an existing branch with that name
		When I create the branch
		Then I should get a branch creation confirmation
	Scenario: Switch To A Different Branch
		Given I have a valid git repo
		And I have a valid branch name
		And I have an existing branch with that name
		When I switch to the branch
		Then I should get the branch object returned to me
	Scenario: Remove An Existing Branch
		Given Given I have a valid git repo
		And I have a valid branch name
		And I have an existing branch
		When I remove the branch from the repo
		Then I should get a branch removal confirmation
	Scenario: Create A New Tag
		Given I have a valid git repo
		And I have a valid tag name
		When I ask for a new tag
		Then I should get a tag confirmation
	Scenario: Get A Tags Commit
		Given I have a valid git repo
		And I have a valid tag name
		And I have an existing tag with that name
		When I ask for that tag
		Then I should get an array of commits that match that tag
	Scenario: Create A New Commit
		Given I have a valid git repo
		And I have files to commit
		And I have a message for the commit
		When I create the commit
		Then the commit message should match the input parameters
	Scenario: Get An Existing Commit
		Given I have a valid git repo
		And I have an existing commit
		And I have a valid commit name
		When I get that commit
		Then I should be returned that commits object
	Scenario: Get A Commits Tree
		Given I have a valid git repo
		And I have a valid commit name
		And I have a valid commit
		When I ask for the commit tree
		Then I should be returned a tree object
	Scenario: Reset Git To Commit
		Given I have a valid git repo
		And I have a valid commit to fall back to
		When I reset git to that commit
		Then I should get a reset confirmation 
		