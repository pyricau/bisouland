Feature: Registration
    In order to get access to the application
    As a visitor
    I need to be able to register

    Scenario: Successfully register with valid email, username and password
        Given I am on homepage
        And I follow "layout.register"

        When I fill in the following:
            | form.username | to.register             |
            | form.email    | to.register@example.com |
            | form.password | password                |
        And I press "registration.submit"

        Then I should see "registration.flash.user_created"

    Scenario Outline: Can not register with invalid email
        Given I am on homepage
        And I follow "layout.register"

        When I fill in the following:
            | form.username | username |
            | form.email    | <Email>  |
            | form.password | password |
        And I press "registration.submit"

        Then I should see "<Error>"

        Examples:
            | Error                       | Email                |
            | fos_user.email.blank        |                      |
            | fos_user.email.long         | johann-gambolputty-de-von-ausfern-schplenden-schlitter-crasscrenbon-fried-digger-dangle-dungle-burstein-von-knacker-thrasher-apple-banger-horowitz-ticolensic-grander-knotty-spelltinkle-grandlich-grumblemeyer-spelterwasser-kurstlich-himbleeisen-bahnwagen-gutenabend-bitte-eine-nurnburger-bratwustle-gerspurten-mit-zweimache-luber-hundsfut-gumberaber-shonendanker-kalbsfleisch-mittler-raucher-von-hautkopft-of-ulm@example.com |
            | fos_user.email.already_used | existing@example.com |
            | fos_user.email.invalid      | fake-email           |

    Scenario Outline: Can not register with invalid username
        Given I am on homepage
        And I follow "layout.register"

        When I fill in the following:
            | form.username | <Username>        |
            | form.email    | email@example.com |
            | form.password | password          |
        And I press "registration.submit"

        Then I should see "<Error>"

        Examples:
            | Error                          | Username             |
            | fos_user.username.blank        |                      |
            | fos_user.username.short        | m                    |
            | fos_user.username.long         | johann-gambolputty-de-von-ausfern-schplenden-schlitter-crasscrenbon-fried-digger-dangle-dungle-burstein-von-knacker-thrasher-apple-banger-horowitz-ticolensic-grander-knotty-spelltinkle-grandlich-grumblemeyer-spelterwasser-kurstlich-himbleeisen-bahnwagen-gutenabend-bitte-eine-nurnburger-bratwustle-gerspurten-mit-zweimache-luber-hundsfut-gumberaber-shonendanker-kalbsfleisch-mittler-raucher-von-hautkopft-of-ulm |
            | fos_user.username.already_used | existing             |

    Scenario Outline: Can not register with invalid password
        Given I am on homepage
        And I follow "layout.register"

        When I fill in the following:
            | form.username | username          |
            | form.email    | email@example.com |
            | form.password | <Password>        |
        And I press "registration.submit"

        Then I should see "<Error>"

        Examples:
            | Error                   | Password |
            | fos_user.password.blank |          |
            | fos_user.password.short | m        |
