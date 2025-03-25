Feature: Edit Book
  In order to update Book information
  As a user
  I need to edit a Book

  Scenario:
    Given there is no book in database
    And a book with ISBN "880450745" and title "1984" and author "George Orwell" and publisher "Fabbri" and price "3" and valutazione "zero" is added
    And I am on "/books/edit"
    When I fill in "880450745" for "ISBN"
    And I press "Ok"
    Then the response should contain "Modifica Libro"
    And the response should contain "880450745"
    When I fill in "Hello World" for "title"
    And I select "Macero" from "Valutazione"
    And I press "Modifica"
    Then the response should contain "Libro modificato!"
    And the response should contain "880450745"
    Given I am on "/books"
    Then the response should contain "880450745"
    And the response should contain "Hello World"
