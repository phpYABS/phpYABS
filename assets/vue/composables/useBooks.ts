import { computed, ref } from "vue";

import { type Book, bookService, formatPrice, PAGE_SIZE } from "../services/bookService";

export function useBooks() {
  const books = ref<Book[]>([]);
  const count = ref(0);
  const offset = ref(0);
  const loading = ref(false);
  const error = ref<Error | null>(null);

  const page = computed(() => Math.floor(offset.value / PAGE_SIZE) + 1);
  const pageCount = computed(() => Math.max(Math.ceil(count.value / PAGE_SIZE), 1));

  let requestId = 0;

  const fetchBooks = async (newOffset = offset.value) => {
    const current = ++requestId;
    loading.value = true;
    error.value = null;

    try {
      const data = await bookService.getBooks(newOffset);
      if (current !== requestId) {
        return; // a newer request superseded this one
      }
      books.value = data.books;
      count.value = data.count;
      offset.value = newOffset;
    } catch (err) {
      if (current !== requestId) {
        return;
      }
      error.value = err instanceof Error ? err : new Error("An error occurred");
      console.error("Error fetching books:", err);
    } finally {
      if (current === requestId) {
        loading.value = false;
      }
    }
  };

  const nextPage = () => fetchBooks(offset.value + PAGE_SIZE);
  const previousPage = () => fetchBooks(Math.max(offset.value - PAGE_SIZE, 0));

  return {
    books,
    count,
    page,
    pageCount,
    loading,
    error,
    fetchBooks,
    nextPage,
    previousPage,
    formatPrice,
  };
}
