Feature: 14 - Listing news
    In order to not miss any announcement
    As a user
    I need to be able to see the complete list of news

    Scenario: Browsing every articles
        Given a complete list of the announcement
        When I ask for the articles
        Then they should be available
