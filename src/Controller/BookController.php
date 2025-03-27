<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\DBAL\Types\Type;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Rate;
use PhpYabs\Form\BookType;
use PhpYabs\ValueObject\ISBN10;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books')]
class BookController extends AbstractController
{
    #[Route('/add', methods: ['GET', 'POST'])]
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

    #[Route('/edit/search', methods: ['GET', 'POST'])]
    public function searchForEdit(Request $request): Response
    {
        $ISBN = $request->get('ISBN');
        if ('' === $ISBN) {
            return $this->modifica($request);
        }

        return $this->redirect("/books/$ISBN/edit");
    }

    #[Route('/{ISBN}/edit', methods: ['GET', 'POST'])]
    #[Route('/edit', methods: ['GET', 'POST'])]
    public function modifica(Request $request): Response
    {
        $ISBN = $request->get('ISBN');
        if (!$ISBN) {
            return $this->render('books/edit.html.twig', ['updated' => false, 'book' => null]);
        }

        $book = $this->entityManager->getRepository(Book::class)->findOneBy(['isbn' => $ISBN]);

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

    #[Route('', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $dbal = $this->getDoctrineConnection();
        $count = $dbal->fetchOne('SELECT COUNT(*) FROM books');
        $offset = $request->query->get('offset', '0');
        if (is_string($offset) && preg_match('/^\\d+$/', $offset)) {
            $offset = intval($offset);
        } else {
            $offset = 0;
        }

        $books = $dbal->fetchAllAssociative(
            'SELECT * FROM books LIMIT ?, 50',
            [$offset],
            [Type::getType('integer')],
        );

        return $this->render('books/list.html.twig', compact('count', 'books'));
    }

    #[Route('/{ISBN}/delete', methods: ['GET', 'POST'])]
    #[Route('/delete', methods: ['GET', 'POST'])]
    public function delete(Request $request): Response
    {
        $vars = ['deleted' => false, 'book' => null, 'rate' => null];

        $ISBN = $request->get('ISBN');
        if ($ISBN) {
            $book = $this->entityManager->getRepository(Book::class)->findOneBy(['isbn' => $ISBN]);
        } else {
            $book = null;
        }

        $delete = $book && isset($_POST['delete']) && 'true' === $_POST['delete'];
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
