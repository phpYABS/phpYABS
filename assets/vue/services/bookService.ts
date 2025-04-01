import z from "zod";

const PriceSchema = z.object({
  currency: z.string().nonempty(),
  amount: z.string().nonempty(),
});

type Price = z.infer<typeof PriceSchema>;

const BookSchema = z.object({
  isbn: z.string().nonempty(),
  title: z.string().nonempty(),
  author: z.string().nonempty(),
  publisher: z.string().nonempty(),
  price: z.string().nonempty(),
  priceObject: PriceSchema,
});

export type Book = z.infer<typeof BookSchema>;

const BookResponseSchema = z.object({
  books: z.array(BookSchema),
  count: z.number(),
});

export type BookResponse = z.infer<typeof BookResponseSchema>;

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
    
    return BookResponseSchema.parseAsync(await response.json());
  }
};

export const formatPrice = (locale?: string) => (price: Price): string => {
  const formatter = new Intl.NumberFormat(locale, {
    style: "currency",
    currency: price.currency,
  });

  return formatter.format(Number(price.amount) / 100);
};
