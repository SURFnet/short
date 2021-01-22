@short_url @ui
Feature: Editing a short url
    In order to update the long url
    As an user
    I want to be able to change the short url

    Background:
        Given I am logged as an user
        And I have shorted the long url "https://long.url/example"

    Scenario: Updating the url
        Given I want to modify the short url for "https://long.url/example"
        When I update it with "https://long.url/new_example"
        And I save the changes
        Then I should be notified that it has been successfully updated
        And it should redirect to "https://long.url/new_example"
