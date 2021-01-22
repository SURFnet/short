@short_url @ui
Feature: Deleting a new short url
    In order to delete short urls I did
    As an user
    I want to be able to delete them from the list

    Background:
        Given I am logged as an user
        And I have shorted the long url "https://long.url/example"

    Scenario: Delete a new short url
        When I delete the short url for "https://long.url/example"
        Then I should be notified that it has been successfully deleted
        And it should appears as deleted in the list
