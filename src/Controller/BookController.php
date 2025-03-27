<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\ORM\EntityManagerInterface;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Rate;
use PhpYabs\Form\BookType;
use PhpYabs\Repository\BookRepository;
use PhpYabs\ValueObject\ISBN10;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books')]
class BookController extends AbstractController
{
    public function __construct(
        EntityManagerInterface $entityManager,
        private readonly BookRepository $bookRepository,
    ) {
        parent::__construct($entityManager);
    }

    #[Route('/add', name: 'book_add', methods: ['GET', 'POST'])]
    public function addAction(Request $request): Response
    {
        $form = $this->createForm(BookType::class);

        $vars = [
            'error' => false,
            'inserted' => false,
            'form' => $form,
        ];

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $book = $form->getData();
            $isbn = ISBN10::fromNineDigits($book->getISBN());
            $book
                ->setISBN($isbn->withoutChecksum)
            ;

            $this->entityManager->persist($book);
            $this->entityManager->flush();
            $vars['inserted'] = true;
        }

        return $this->render('books/add.html.twig', $vars);
    }

    #[Route('/edit', name: 'book_search_for_edit', methods: ['GET', 'POST'])]
    public function searchForEditAction(Request $request): Response
    {
        $ISBN = $request->get('ISBN');
        if ($ISBN) {
            return $this->redirectToRoute('book_edit', ['ISBN' => $ISBN]);
        }

        return $this->render('books/edit.html.twig', ['updated' => false, 'book' => null]);
    }

    #[Route('/{ISBN}/edit', name: 'book_edit', methods: ['GET', 'POST'])]
    public function modifica(Request $request, string $ISBN): Response
    {
        $book = $this->bookRepository->findOneBy(['isbn' => $ISBN]);

        if ('GET' === $request->getMethod()) {
            if ($book) {
                $valutazione = $book->getRate()?->value;
                switch ($valutazione) {
                    case 'zero':
                        $selzero = 'selected';
                        break;
                    case 'rotmed':
                        $selrotmed = 'selected';
                        break;
                    case 'rotsup':
                        $selrotsup = 'selected';
                        break;
                    case 'buono':
                        $selbuono = 'selected';
                        break;
                    default:
                        $selnull = 'selected';
                        break;
                }

                return $this->render('books/edit.html.twig', [
                    'book' => $book,
                    'selzero' => $selzero ?? null,
                    'selrotmed' => $selrotmed ?? null,
                    'selrotsup' => $selrotsup ?? null,
                    'selbuono' => $selbuono ?? null,
                    'selnull' => $selnull ?? null,
                    'updated' => false,
                ]);
            }
        }

        if ('POST' === $request->getMethod()) {
            $parsedBody = $request->request->all();

            $book
                ->setIsbn($parsedBody['ISBN'])
                ->setTitle($parsedBody['title'])
                ->setAuthor($parsedBody['author'])
                ->setPublisher($parsedBody['publisher'])
                ->setPrice($parsedBody['price'])
            ;

            $this->addRate($request, $book);
            $this->entityManager->persist($book);
            $this->entityManager->flush();
        }

        return $this->render('books/edit.html.twig', [
            'book' => $book,
            'updated' => true,
        ]);
    }

    #[Route('', name: 'book_list', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $count = $this->bookRepository->countAll();
        $offset = $request->query->get('offset', '0');
        if (is_string($offset) && preg_match('/^\\d+$/', $offset)) {
            $offset = intval($offset);
        } else {
            $offset = 0;
        }

        $books = $this->bookRepository->findPaginated($offset, 50);

        return $this->render('books/list.html.twig', compact('count', 'books'));
    }

    #[Route('/{ISBN}/delete', name: 'book_delete', methods: ['GET', 'POST'])]
    #[Route('/delete', name: 'book_search_for_delete', methods: ['GET', 'POST'])]
    public function delete(Request $request): Response
    {
        $vars = ['deleted' => false, 'book' => null, 'rate' => null];

        $ISBN = $request->get('ISBN');
        if ($ISBN) {
            $book = $this->bookRepository->findOneBy(['isbn' => $ISBN]);
        } else {
            $book = null;
        }

        $delete = $book && 'true' === $request->request->get('delete');
        if ($delete) {
            $this->entityManager->remove($book);
            $this->entityManager->flush();
            $vars['deleted'] = true;
        } else {
            $vars['book'] = $book;
            $vars['rate'] = $book?->getRate()->value;
        }

        return $this->render('books/delete.html.twig', $vars);
    }

    private function addRate(Request $request, Book $book): void
    {
        $rate = $request->get('rate');
        if ($rate) {
            $book->setRate(Rate::tryFrom($rate));
        }
    }
}
