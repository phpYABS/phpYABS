<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\DBAL\Types\Type;
use PhpYabs\DB\Book;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/books')]
class BookController extends AbstractController
{
    #[Route('/add', methods: ['GET', 'POST'])]
    public function addAction(Request $request): Response
    {
        $addbook = new Book($this->getDoctrineConnection());

        $vars = [
            'error' => false,
            'inserted' => false,
        ];

        if ('POST' === $request->getMethod() && $addbook->isValidISBN($_POST['ISBN'] ?? '')) {
            $fields = [
                'ISBN' => $_POST['ISBN'],
                'title' => $_POST['title'],
                'author' => $_POST['author'],
                'publisher' => $_POST['publisher'],
                'price' => $_POST['price'],
            ];

            $addbook->setFields($fields);
            $addbook->setRate($_POST['rate']);

            $vars['inserted'] = $addbook->saveToDB();
            $vars['error'] = !$vars['inserted'];
        }

        return $this->render('books/add.twig', $vars);
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
        $book = new Book($this->getDoctrineConnection());

        $ISBN = $request->get('ISBN');
        if (!$ISBN) {
            return $this->render('books/edit.twig', ['updated' => false, 'book' => null]);
        }

        if ('GET' === $request->getMethod()) {
            $book->getFromDB($ISBN);

            if ($f = $book->getFields()) {
                $valutazione = $book->getRate();
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

                return $this->render('books/edit.twig', [
                    'book' => $f,
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

            $fields = ['ISBN' => $parsedBody['ISBN'],
                'title' => $parsedBody['title'],
                'author' => $parsedBody['author'],
                'publisher' => $parsedBody['publisher'],
                'price' => $parsedBody['price'],
            ];

            $book->setFields($fields);
            $book->setRate($parsedBody['rate']);
        }

        return $this->render('books/edit.twig', [
            'book' => $book->getFields(),
            'updated' => $book->saveToDB(),
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

        $books = $dbal->fetchFirstColumn(
            'SELECT ISBN FROM books LIMIT ?, 50',
            [$offset],
            [Type::getType('integer')],
        );

        $books = array_map(function (string $ISBN) use ($dbal) {
            $book = new Book($dbal);
            $book->getFromDB($ISBN);

            return $book->getFields();
        }, $books);

        return $this->render('books/list.twig', compact('count', 'books'));
    }

    #[Route('/{ISBN}/delete', methods: ['GET', 'POST'])]
    #[Route('/delete', methods: ['GET', 'POST'])]
    public function delete(Request $request): Response
    {
        $vars = ['deleted' => false, 'book' => null, 'rate' => null];

        $book = new Book($this->getDoctrineConnection());
        $ISBN = $request->get('ISBN');
        if ($ISBN) {
            $book->getFromDB($ISBN);
        }

        $delete = isset($_POST['delete']) && 'true' === $_POST['delete'];
        if ($delete) {
            $vars['deleted'] = $book->delete();
        } else {
            $vars['book'] = $book->getFields();
            $vars['rate'] = $book->getRate();
        }

        return $this->render('books/delete.twig', $vars);
    }
}
