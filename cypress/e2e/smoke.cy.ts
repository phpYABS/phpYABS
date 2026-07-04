// Minimal smoke tests: the app boots and the main pages respond.
describe("smoke", () => {
  it("redirects the homepage to the current purchase", () => {
    cy.visit("/");
    cy.location("pathname").should("eq", "/purchases/current");
    cy.get("body").should("be.visible");
  });

  it("serves the purchase list", () => {
    cy.request("/purchases").its("status").should("eq", 200);
  });

  it("starts a new empty cart", () => {
    cy.visit("/purchases/new");
    cy.contains("Valutazione dei libri in acquisto");
    cy.contains("0 libri acquistati");
    // the amount and € may be separated by a non-breaking space
    cy.contains("li", "Totale contanti").contains("0,00");
    cy.contains("li", "Totale buono").contains("0,00");
  });
});
