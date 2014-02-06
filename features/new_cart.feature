Feature: New cart
  In order to begin a new purchase
  As a user
  I need to create a new cart

  Scenario:
    Given I am on "new cart" page
    Then the response should contain "Valutazione dei libri in acquisto"
    Then the response should contain "0 Libri acquistati"
    And the response should contain "Totale contanti: 0.00 €"
    And the response should contain "Totale buono: 0.00 €"
    And the response should contain "Totale rottamazione: 0.00 €"
