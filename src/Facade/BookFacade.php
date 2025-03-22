<?php

namespace PhpYabs\Facade;

use Doctrine\DBAL\Types\Type;
use PhpYabs\DB\Book;
use Psr\Http\Message\ResponseInterface;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class BookFacade extends AbstractFacade
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
            $addbook->setCondition($_POST['condition']);

            $vars['inserted'] = $addbook->saveToDB();
            $vars['error'] = !$vars['inserted'];
        }

        $view = Twig::fromRequest($request);

        return $view->render($response, 'books/add.twig', $vars);
    }

    public function modifica(Request $request, Response $response): ResponseInterface
    {
        if (!isset($_GET['ISBN'])) {
            $view = Twig::fromRequest($request);

            return $view->render($response, 'books/edit.twig');
        }

        ob_start(); ?>
        <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
        <html>
        <head>
            <title>Modifica libro</title>
            <link href="/css/main.css" rel="stylesheet" type="text/css">
        </head>
        <body>
        <script language="JavaScript" type="text/javascript">
            document.form1.ISBN.focus()
        </script>
        <h1 align="center">Modifica Libro</h1>
        <?php
        global $dbal;
        $modbook = new Book($dbal);

        $fields = ['ISBN' => $_GET['ISBN'],  'title' => $_GET['title'], 'author' => $_GET['author'],
            'publisher' => $_GET['publisher'], 'price' => $_GET['price'], ];

        $modbook->setFields($fields);
        $modbook->setCondition($_GET['condition']);

        if ($modbook->saveToDB()) {
            echo '<p align="center">Libro modificato!</p>';
        }

        $modbook->getFromDB($_GET['ISBN']);

        if ($f = $modbook->getFields()) {
            [$ISBN, $Titolo, $Autore, $Editore, $Prezzo] = $f;
            $valutazione = $modbook->getCondition();
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

            include PATH_TEMPLATES . '/oldones/libri/tabmod.php';
        } else { ?>
            <p align="center">Libro non trovato</p>
            <?php
        }
        ?>
        </body>
        </html>
<?php
        $response->getBody()->write((string) ob_get_clean());

        return $response;
    }

    public function elenco(Request $request, Response $response): ResponseInterface
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
}
