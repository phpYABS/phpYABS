import z from "zod";

export const PAGE_SIZE = 50;

const PriceSchema = z.object({
  currency: z.string().nonempty(),
  amount: z.string().nonempty(),
});

type Price = z.infer<typeof PriceSchema>;

// legacy 2003 data: text fields may be empty, only the ISBN is guaranteed
const BookSchema = z.object({
  isbn: z.string().nonempty(),
  title: z.string(),
  author: z.string(),
  publisher: z.string(),
  price: PriceSchema.nullable(),
});

export type Book = z.infer<typeof BookSchema>;

const BookResponseSchema = z.object({
  books: z.array(BookSchema),
  count: z.number(),
});

export type BookResponse = z.infer<typeof BookResponseSchema>;

export const bookService = {
  async getBooks(offset = 0): Promise<BookResponse> {
    const params = new URLSearchParams({ offset: String(offset) });
    const response = await fetch(`/books?${params}`, {
      headers: {
        "Accept": "application/json"
      }
    });

    if (!response.ok) {
      throw new Error("Failed to fetch books");
    }

    return BookResponseSchema.parseAsync(await response.json());
  }
};

export const formatPrice = (locale?: string) => (price: Price | null): string => {
  if (!price) {
    return "";
  }

  const formatter = new Intl.NumberFormat(locale, {
    style: "currency",
    currency: price.currency,
  });

  return formatter.format(Number(price.amount) / 100);
};
