Feature: 09 - Writing news
    In order to keep users informed
    As an administrator
    I need to write news

    Scenario: Writing an article
        Given there is something to announce
        When I write an article about it
        Then it should be available
