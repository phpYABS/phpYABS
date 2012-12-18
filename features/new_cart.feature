Feature: New cart
  In order to begin a new purchase
  As a user
  I need to create a new cart

  Scenario:
    Given I am on "new cart" page
    Then Text "Valutazione dei libri in acquisto" should be present
    Then Text "0 books purchased" should be present
    And Text "Totale contanti: 0.00 €" should be present
    And Text "Totale buono: 0.00 €" should be present
    And Text "Totale rottamazione: 0.00 €" should be present
