@short_url @ui
Feature: Generating a new short url
    In order to generate a short url
    As an user
    I want to add a new long url to my list

    Background:
        Given I am logged as an user

    Scenario: Generate a new short url
        When I want to generate a new short url
        And I write the long url "https://long.url/example"
        And I short it
        Then I should see 1 shortened urls on my list

