Feature: Login
    In order to get access to the application
    As a registered person
    I need to be able to authenticate

    Scenario: Successfully authenticating with correct credentials
        Given I am on homepage
        And I follow "layout.login"

        When I fill in the following:
            | security.login.username | to.login |
            | security.login.password | password |
        And I press "security.login.submit"

        Then I should see "layout.logout"

    Scenario: Can not authenticate with bad credentials
        Given I am on homepage
        And I follow "layout.login"

        When I fill in the following:
            | security.login.username | wrong.login |
            | security.login.password | password    |
        And I press "security.login.submit"

        Then I should see "Bad credentials"
