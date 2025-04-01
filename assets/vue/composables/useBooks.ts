import { ref } from "vue";

import { type Book, bookService } from "../services/bookService";

export function useBooks() {
  const books = ref<Book[]>([]);
  const count = ref(0);
  const loading = ref(false);
  const error = ref<Error | null>(null);

  const fetchBooks = async () => {
    loading.value = true;
    error.value = null;
    
    try {
      const data = await bookService.getBooks();
      books.value = data.books;
      count.value = data.count;
    } catch (err) {
      error.value = err instanceof Error ? err : new Error("An error occurred");
      console.error("Error fetching books:", err);
    } finally {
      loading.value = false;
    }
  };

  return {
    books,
    count,
    loading,
    error,
    fetchBooks
  };
} 
