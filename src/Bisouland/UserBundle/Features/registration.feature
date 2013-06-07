Feature: Registration
    In order to become a user
    As a visitor
    I need to be able to register

    Scenario: Register with username, email and password
        Given I am on "/register"

        When I fill in the following:
            | form.username | to.register |
            | form.email | to.register@example.com |
            | form.password | password |
        And I press "registration.submit"

        Then I should see "registration.flash.user_created"

    Scenario: Fail registration with existing username and email
        Given I am on "/register"

        When I fill in the following:
            | form.username | existing |
            | form.email | existing@example.com |
            | form.password | password |
        And I press "registration.submit"

        Then I should see "fos_user.username.already_used"
        And I should see "fos_user.email.already_used"
