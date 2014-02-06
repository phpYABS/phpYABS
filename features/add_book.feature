Feature: Add book
  In order to get a book information saved
  As a user
  I need to add a book

  Scenario: Add a book
    Given there is no book in database
    And I am on "add book" page
    When I fill in the following:
    | ISBN    | 880450745     |
    | Titolo  | 1984          |
    | Autore  | George Orwell |
    | Editore | Fabbri        |
    | Prezzo  | 3             |
    And I select "Macero" from "Valutazione"
    And I press "Aggiungi"
    Then the response should contain "Libro inserito"
    Given I am on "book list" page
    Then I should see book fields
