Feature:
    In order to change my personal information
    As a user
    I need to be able to edit or remove my account

    Scenario: Change username and email with password
        Given I am logged in as "change"
        And I am on "/profile"

        When I fill in the following:
            | form.username | changed |
            | form.email | changed@example.com |
            | form.current_password | password |
        And press "profile.edit.submit"

        Then I should see "profile.flash.updated"

    Scenario: Fail changing username and email to existing ones, with password
        Given I am logged in as "changed"
        And I am on "/profile"

        When I fill in the following:
            | form.username | existing |
            | form.email | existing@example.com |
            | form.current_password | password |
        And press "profile.edit.submit"

        Then I should see "fos_user.username.already_used"
