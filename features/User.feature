Feature: User
	In order to have a centralized user system
	As a node script
	I want to be able to manipulate users from a central node script
	
	Background:
		Given I have a mongodb connection
	Scenario: User Login
		Given I have entered a username "testuser"
		And I have entered a password "testpassword"
		When I press user login
		Then the result should be true on the screen
		
	Scenario: Watch Logged In User
		Given I have logged in a user with username "testuser" and password "testpassword"
		And I have a valid unique session id "onetwobluecan"
		And I have no other user in memory with that username, password, or session id
		When I press user watch
		Then the result should be true on the screen
		
	Scenario: Register User
		Given I have entered a username "testuser"
		And I have entered a password "testpassword"
		And I have entered an email "testemail"
		And I have entered a password confirmation "testpassword"
		And I do not have any users already with that username and email
		And the username, email, and password all successfully run through their own validation steps
		When I press register user
		Then the result should be true on the screen
		
	Scenario: Check Singular User Permission
		Given I have a user with username "testuser"
		And I have logged him in using password "testpassword"
		And I have added him to the active user list with session id "onetwobluecan"
		And I have a permission "testperm" that I want to check
		And I know that I have access to that permission
		When I press check user permission
		Then the result should be true on the screen
		
	Scenario: Create New User Object
		Given I want to create a new user object
		When I ask for one
		Then the result should be an empty user object
		
	Scenario: Save User Object
		Given I have a user object
		And I have all the required field for the user object
		And all the fields for the user object pass validation
		When I click save
		Then the result should be true
		
	Scenario: Find Users
		Given I have a username
		When I click find
		Then the result should be an array with one entry with the username