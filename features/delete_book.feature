Feature: Delete Book
  In order to remove Book information
  As a user
  I need to delete a Book

  Scenario:
    Given there is no book in database
    And a book with ISBN "880450745" and title "1984" and author "George Orwell" and publisher "Fabbri" and price "3" and valutazione "zero" is added
    And I am on book delete page
    When I type "880450745" on "ISBN" field
    And I click on "Ok" button
    And I click on "Delete Book" link
    Then I should get a "book successfully deleted" message
    Given I am on book list page
    Then I should see no book