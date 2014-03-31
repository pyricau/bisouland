Feature: 01 - Register
    In order to access the game
    As a visitor
    I need to register

    Scenario: Creating an account
        Given I have a username
        And I have a password
        When I provide those credentials
        Then my account should be created
