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
    Then Text "Modifica Libro" should be present
    And Text "880450745" should be present
    When I type "Hello World" on "title" field
    And I select "Macero" on field "Valutazione"
    And I press "Modifica"
    Then Text "book updated" should be present
    And Text "880450745" should be present
    Given I am on "book list" page
    Then the response should contain "880450745"
    And the response should contain "Hello World"

