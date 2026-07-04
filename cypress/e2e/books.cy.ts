// Book lifecycle smoke test: add, edit, delete (ports the old Behat features).
// Runs against the dockerized app, whose database persists between runs,
// so each run works on a book with a freshly generated ISBN.

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

const isbn = randomIsbn13();
const title = `Smoke ${isbn}`;

describe("book lifecycle", () => {
  it("adds a book", () => {
    cy.visit("/books/add");
    cy.get("input[name='book[isbn]']").type(isbn);
    cy.get("input[name='book[title]']").type(title);
    cy.get("input[name='book[author]']").type("George Orwell");
    cy.get("input[name='book[publisher]']").type("Fabbri");
    cy.get("input[name='book[price][tbbc_amount]']").type("3");
    cy.get("form.sf-form button[type=submit]").click();
    cy.contains("Libro inserito");
  });

  it("shows the book in the list", () => {
    cy.visit("/books");
    cy.contains("td", isbn);
    cy.contains("td", title);
  });

  it("edits the book", () => {
    cy.visit("/books/edit");
    cy.get("input[name='ISBN']").type(isbn);
    cy.get("input[name='ISBN']").closest("form").submit();

    cy.get("input[name='book[title]']").clear().type(`${title} edited`);
    cy.get("input[name='book[title]']").closest("form").submit();
    cy.contains("Libro modificato");
  });

  it("deletes the book", () => {
    cy.visit("/books/delete");
    cy.get("input[name='ISBN']").type(isbn);
    cy.get("input[name='ISBN']").closest("form").submit();

    cy.contains(`${title} edited`);
    cy.get("form.book-actions button[type=submit]").click();
    cy.contains("Libro cancellato!");

    cy.visit("/books");
    cy.contains("td", isbn).should("not.exist");
  });
});
