Feature: Registration
    In order to become a user
    As a visitor
    I need to be able to register

    Scenario: Registration
        Given I am on "/register"

        When I fill in the following:
            | form.username | to.register |
            | form.email | to.register@example.com |
            | form.password | password |
        And I press "registration.submit"

        Then I should see "layout.logout"

    Scenario: Registration failure
        Given I am on "/register"

        When I fill in the following:
            | form.username | to.register |
            | form.email | to.register@example.com |
            | form.password | password |
        And I press "registration.submit"

        Then I should not see "layout.logout"
