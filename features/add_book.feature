Feature: Add book
  In order to get a book information saved
  As a user
  I need to add a book

  Scenario: Add a book
    Given there is no book in database
    And I am on "/books/add"
    When I fill in the following:
    | ISBN      | 880450745     |
    | title     | 1984          |
    | author    | George Orwell |
    | publisher | Fabbri        |
    | price     | 3             |
    And I select "Macero" from "rate"
    And I press "Aggiungi"
    Then the response should contain "Libro inserito"
    Given I am on "/books"
    Then I should see "880450745"
    And I should see "1984"
    And I should see "George Orwell"
    And I should see "Fabbri"
    And I should see "3.00"
