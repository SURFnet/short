@short_url @ui
Feature: Generating a new short url
    In order to generate a custom short url
    As an administrator
    I want to add a new long url to my list with its code

    Background:
        Given I am logged as an administrator

    Scenario: Generate a new short url
        When I want to generate a new custom short url
        And I write the long url "https://long.url/example"
        And I write the short code "example"
        And I short it
        Then I should see 1 shortened urls on my list
