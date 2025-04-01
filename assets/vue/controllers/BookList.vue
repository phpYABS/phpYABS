<script setup lang="ts">
import { onMounted } from 'vue'
import { useBooks } from '../composables/useBooks'
import {
  trans,
  BOOK_FIELDS_ISBN,
  BOOK_FIELDS_TITLE,
  BOOK_FIELDS_AUTHOR,
  BOOK_FIELDS_PUBLISHER,
  BOOK_FIELDS_PRICE,
  BOOK_LIST_COUNT,
} from "../../translator";

const { books, count, fetchBooks } = useBooks()

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
      <tr v-for="book in books">
        <td>{{ book.isbn }}</td>
        <td>{{ book.title }}</td>
        <td>{{ book.author }}</td>
        <td>{{ book.publisher }}</td>
        <td>{{ book.price }}</td>
      </tr>
      </tbody>
    </table>
    <p class="book-count">{{ trans(BOOK_LIST_COUNT, { "%count%": count }) }}</p>
  </section>

</template>

<style lang="scss" scoped>

</style>
