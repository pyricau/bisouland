Feature: 15 - Reading news
    In order to keep myself informed
    As a user
    I need to be able to read an article

    Scenario: Reading an article
        Given an announcement
        When I ask for the article
        Then it should be available
