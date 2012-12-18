Feature: Delete Book
  In order to remove Book information
  As a user
  I need to delete a Book

  Scenario:
    Given there is no book in database
    And a book with ISBN "880450745" and title "1984" and author "George Orwell" and publisher "Fabbri" and price "3" and valutazione "zero" is added
    And I am on "new cart" page
    When I type "880450745" on "newISBN" field
    And I click on "Ok" button
