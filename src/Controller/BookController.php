<?php

declare(strict_types=1);

namespace PhpYabs\Controller;

use Doctrine\DBAL\Types\Type;
use PhpYabs\DB\Book;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class BookController extends AbstractController
{
    public function aggiungi(Request $request, Response $response): ResponseInterface
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

        $view = Twig::fromRequest($request);

        return $view->render($response, 'books/add.twig', $vars);
    }

    public function searchForEdit(Request $request, Response $response): ResponseInterface
    {
        $body = $request->getParsedBody();
        assert(is_array($body));

        $ISBN = $body['ISBN'] ?? '';
        if ('' === $ISBN) {
            return $this->modifica($request, $response, []);
        }

        return $response->withStatus(302)->withHeader('Location', "/books/$ISBN/edit");
    }

    public function modifica(Request $request, Response $response, array $parameters): ResponseInterface
    {
        $view = Twig::fromRequest($request);
        $book = new Book($this->getDoctrineConnection());

        if (!isset($parameters['ISBN'])) {
            return $view->render($response, 'books/edit.twig');
        }

        if ('GET' === $request->getMethod()) {
            $book->getFromDB($parameters['ISBN']);

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

                return $view->render($response, 'books/edit.twig', [
                    'book' => $f,
                    'selzero' => $selzero ?? null,
                    'selrotmed' => $selrotmed ?? null,
                    'selrotsup' => $selrotsup ?? null,
                    'selbuono' => $selbuono ?? null,
                    'selnull' => $selnull ?? null,
                ]);
            }
        }

        if ('POST' === $request->getMethod()) {
            $parsedBody = $request->getParsedBody() ?? [];

            $fields = ['ISBN' => $parsedBody['ISBN'],
                'title' => $parsedBody['title'],
                'author' => $parsedBody['author'],
                'publisher' => $parsedBody['publisher'],
                'price' => $parsedBody['price'],
            ];

            $book->setFields($fields);
            $book->setRate($parsedBody['rate']);
        }

        return $view->render($response, 'books/edit.twig', [
            'book' => $book->getFields(),
            'updated' => $book->saveToDB(),
        ]);
    }

    public function index(Request $request, Response $response): ResponseInterface
    {
        $dbal = $this->getDoctrineConnection();
        $count = $dbal->fetchOne('SELECT COUNT(*) FROM books');
        $offset = $request->getQueryParams()['offset'] ?? 0;
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

        $view = Twig::fromRequest($request);

        return $view->render($response, 'books/list.twig', compact('count', 'books'));
    }

    public function delete(Request $request, Response $response, array $parameters): ResponseInterface
    {
        $vars = ['deleted' => false, 'book' => null, 'rate' => null];
        $view = Twig::fromRequest($request);

        $book = new Book($this->getDoctrineConnection());
        if (isset($_POST['ISBN'])) {
            $book->getFromDB($_POST['ISBN']);
        } elseif (isset($parameters['ISBN'])) {
            $book->getFromDB($parameters['ISBN']);
        }

        $delete = isset($_POST['delete']) && 'true' === $_POST['delete'];
        if ($delete) {
            $vars['deleted'] = $book->delete();
        } else {
            $vars['book'] = $book->getFields();
            $vars['rate'] = $book->getRate();
        }

        return $view->render($response, 'books/delete.twig', $vars);
    }
}
