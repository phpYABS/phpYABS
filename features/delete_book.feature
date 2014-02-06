Feature: Delete Book
  In order to remove Book information
  As a user
  I need to delete a Book

  Scenario:
    Given there is no book in database
    And a book with ISBN "880450745" and title "1984" and author "George Orwell" and publisher "Fabbri" and price "3" and valutazione "zero" is added
    And I am on "delete book" page
    When I fill in "880450745" for "ISBN"
    And I press "Ok"
    And I follow "Cancella Libro"
    Then the response should contain "Libro Cancellato!"
    Given I am on "book list" page
    Then the response should contain "0 libri presenti"
