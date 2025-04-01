<script setup lang="ts">
import { onMounted } from "vue"

import {
  BOOK_FIELDS_AUTHOR,
  BOOK_FIELDS_ISBN,
  BOOK_FIELDS_PRICE,
  BOOK_FIELDS_PUBLISHER,
  BOOK_FIELDS_TITLE,
  BOOK_LIST_COUNT,
  trans,
} from "../../translator";
import { useBooks } from "../composables/useBooks"

const { books, count, fetchBooks, formatPrice } = useBooks()
const props = defineProps(["locale"]);
const fmt = formatPrice(props.locale);

onMounted(() => {
  fetchBooks()
})
</script>

<template>
  <section class="book-list">
    <table class="book-table">
      <thead>
        <tr>
          <th>{{ trans(BOOK_FIELDS_ISBN) }}</th>
          <th>{{ trans(BOOK_FIELDS_TITLE) }}</th>
          <th>{{ trans(BOOK_FIELDS_AUTHOR) }}</th>
          <th>{{ trans(BOOK_FIELDS_PUBLISHER) }}</th>
          <th>{{ trans(BOOK_FIELDS_PRICE) }}</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="book in books"
          :key="book.isbn"
        >
          <td>{{ book.isbn }}</td>
          <td>{{ book.title }}</td>
          <td>{{ book.author }}</td>
          <td>{{ book.publisher }}</td>
          <td>{{ fmt(book.priceObject) }}</td>
        </tr>
      </tbody>
    </table>
    <p class="book-count">
      {{ trans(BOOK_LIST_COUNT, { "%count%": count }) }}
    </p>
  </section>
</template>

<style lang="scss" scoped>
.book-list {
  padding: 1.5rem;

  .book-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 8px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    margin-bottom: 1.5rem;

    thead {
      background-color: #f8f9fa;

      th {
        padding: 1rem;
        text-align: left;
        font-weight: 600;
        color: #495057;
        border-bottom: 2px solid #dee2e6;
        white-space: nowrap;
      }
    }

    tbody {
      tr {
        &:hover {
          background-color: #f8f9fa;
        }

        &:not(:last-child) {
          border-bottom: 1px solid #dee2e6;
        }
      }

      td {
        padding: 1rem;
        color: #212529;
      }
    }
  }

  .book-count {
    color: #6c757d;
    font-size: 0.9rem;
    margin: 0;
  }

  .loading {
    text-align: center;
    padding: 2rem;
    color: #6c757d;
  }

  .error-message {
    background-color: #fff3f3;
    color: #dc3545;
    padding: 1rem;
    border-radius: 8px;
    border: 1px solid #ffcdd2;
    margin-bottom: 1rem;
  }

  // Responsive design
  @media (max-width: 768px) {
    padding: 1rem;

    .book-table {
      display: block;
      overflow-x: auto;
      -webkit-overflow-scrolling: touch;

      th, td {
        padding: 0.75rem;
      }
    }
  }

  // Fade transition
  .fade-enter-active,
  .fade-leave-active {
    transition: opacity 0.3s ease;
  }

  .fade-enter-from,
  .fade-leave-to {
    opacity: 0;
  }
}
</style>
