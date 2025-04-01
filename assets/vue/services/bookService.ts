export interface Book {
  isbn: string;
  title: string;
  author: string;
  publisher: string;
  price: number;
}

interface BookResponse {
  books: Book[];
  count: number;
}

export const bookService = {
  async getBooks(): Promise<BookResponse> {
    const response = await fetch("/books", {
      headers: {
        "Accept": "application/json"
      }
    });
    
    if (!response.ok) {
      throw new Error("Failed to fetch books");
    }
    
    return response.json();
  }
}; 
