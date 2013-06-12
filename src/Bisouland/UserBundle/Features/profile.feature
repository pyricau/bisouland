Feature:
    In order to change my personal information
    As a user
    I need to be able to edit or remove my account

    Scenario: Change username and email with password
        Given I am logged in as "change"
        And I am on "/account"

        When I fill in the following:
            | form.username | changed |
            | form.email | changed@example.com |
            | form.current_password | password |
        And press "profile.edit.submit"

        Then I should see "profile.flash.updated"

    Scenario: Fail changing username and email to existing ones, with password
        Given I am logged in as "changed"
        And I am on "/account"

        When I fill in the following:
            | form.username | existing |
            | form.email | existing@example.com |
            | form.current_password | password |
        And press "profile.edit.submit"

        Then I should see "fos_user.username.already_used"
        And I should see "fos_user.email.already_used"

    Scenario: Remove the account
        Given I am logged in as "to.remove"
        And I am on "/account"

        When I follow "profile.removal.button"

        Then I should see "account.removal_confirmation.title"

        When I press "account.removal_confirmation.button"

        Then I should see "account.removal_confirmation.flash"
