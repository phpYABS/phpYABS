Feature: Edit Book
  In order to update Book information
  As a user
  I need to edit a Book

  Scenario:
    Given there is no book in database
    And a book with ISBN "880450745" and title "1984" and author "George Orwell" and publisher "Fabbri" and price "3" and valutazione "zero" is added
    And I am on "edit book" page
    When I type "880450745" on "ISBN" field
    And I press "Ok"
    Then the response should contain "Modifica Libro"
    And the response should contain "880450745"
    When I type "Hello World" on "title" field
    And I select "Macero" from "Valutazione"
    And I press "Modifica"
    Then the response should contain "Libro modificato!"
    And the response should contain "880450745"
    Given I am on "book list" page
    Then the response should contain "880450745"
    And the response should contain "Hello World"

