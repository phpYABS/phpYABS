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
});
