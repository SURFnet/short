@short_url @ui
Feature: Short URL validation
    In order to avoid mistakes when I want to generate new short URLs
    As an user
    I want to be warned that the URL does not follow the rules

    Background:
        Given I am logged as an user

    Scenario: Adding a blank url
        When I want to generate a new short url
        And I short it
        Then I should see the URL should not be blank

    Scenario Outline: Adding and invalid long url
        When I want to generate a new short url
        And I write the long url "<url>"
        And I short it
        Then I should see the URL is invalid
        Examples:
            | url                    |
            | long.url/example       |
            | //long.url/example     |

    Scenario Outline: Adding a forbidden domain
        When I want to generate a new short url
        And I write the long url "<url>"
        And I short it
        Then I should see that target URL may not start with "<domain>"
        Examples:
            | url                              | domain           |
            | https://bit.ly/example           | bit.ly           |
            | https://edulnk.localhost/example | edulnk.localhost |
