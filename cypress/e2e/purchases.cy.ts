// Purchase flow smoke test: scan a book into a fresh cart, then remove the line.

function randomIsbn13(): string {
  let core = "";
  for (let i = 0; i < 9; i++) {
    core += Math.floor(Math.random() * 10);
  }
  const digits = `978${core}`;
  let sum = 0;
  for (let i = 0; i < 12; i++) {
    sum += Number(digits[i]) * (i % 2 === 0 ? 1 : 3);
  }
  return digits + ((10 - (sum % 10)) % 10);
}

describe("purchase flow", () => {
  it("scans a book into a new cart and removes the line", () => {
    const isbn = randomIsbn13();
    const title = `Cart ${isbn}`;

    // a book to scan
    cy.visit("/books/add");
    cy.get("input[name='book[isbn]']").type(isbn);
    cy.get("input[name='book[title]']").type(title);
    cy.get("input[name='book[author]']").type("George Orwell");
    cy.get("input[name='book[publisher]']").type("Fabbri");
    cy.get("input[name='book[price][tbbc_amount]']").type("3");
    cy.get("form.sf-form button[type=submit]").click();
    cy.contains("Libro inserito");

    // scan it into a fresh cart
    cy.visit("/purchases/new");
    cy.contains("0 libri acquistati");
    cy.get("input[name='newISBN']").type(isbn);
    cy.get("form[name='libro'] button[type=submit]").click();
    cy.contains("1 libri acquistati");
    cy.contains("td", title);

    // remove the line
    cy.get(".book-actions form button[type=submit]").click();
    cy.contains("0 libri acquistati");
    cy.contains("td", title).should("not.exist");

    // clean up the book so reruns start from scratch
    cy.visit("/books/delete");
    cy.get("input[name='ISBN']").type(isbn);
    cy.get("input[name='ISBN']").closest("form").submit();
    cy.get("form.book-actions button[type=submit]").click();
    cy.contains("Libro cancellato!");
  });
});
