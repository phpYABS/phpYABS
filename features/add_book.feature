Feature: Add book
  In order to get a book information saved
  As a user
  I need to add a book

  Scenario: Add a book
    Given there is no book in database
    And I am on "add book" page
    When I type "880450745" on "ISBN" field
    And  I type "1984" on "title" field
    And  I type "George Orwell" on "author" field
    And  I type "Fabbri" on "publisher" field
    And  I type "3" on "price" field
    And I select "Macero" from "Valutazione"
    And I press "Aggiungi"
    Then the response should contain "Libro inserito"
    Given I am on "book list" page
    Then I should see book fields
