Feature: Logout
    In order to free the access to the application
    As a user
    I need to be able to logout

    Scenario: Logout
        Given I am logged in as "to.logout"

        When I follow "layout.logout"

        Then I should see "layout.login"
