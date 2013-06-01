Feature: Login
    In order to have access to the application
    As a registered person
    I need to be able to login

    Scenario: Login with username and password
        Given I am on "/login"

        When I fill in the following:
            | security.login.username | to.login |
            | security.login.password | password |
        And I press "security.login.submit"

        Then I should see "layout.logout"

    Scenario: Fail login with wrong username and password
        Given I am on "/login"

        When I fill in the following:
            | security.login.username | wrong.login |
            | security.login.password | password |
        And I press "security.login.submit"

        Then I should see "Bad credentials"
