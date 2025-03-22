<?php

namespace PhpYabs\Facade;

use Doctrine\DBAL\Types\Type;
use PhpYabs\DB\Book;
use Slim\Psr7\Request;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class BookFacade extends AbstractFacade
{
    public function aggiungi(Request $request, Response $response): Response
    {
        $addbook = new Book($this->getDoctrineConnection());

        $vars = [
            'error' => false,
            'inserted' => false,
        ];

        if ($request->getMethod() === 'POST' && $addbook->isValidISBN($_POST['ISBN']) ?? ''){
            $fields = [
                'ISBN' => $_POST['ISBN'],
                'title' => $_POST['title'],
                'author' => $_POST['author'],
                'publisher' => $_POST['publisher'],
                'price' => $_POST['price']
            ];

            $addbook->setFields($fields);
            $addbook->setCondition($_POST['condition']);

            $vars['inserted'] = $addbook->saveToDB();
            $vars['error'] = !$vars['inserted'];
        }

        $view = Twig::fromRequest($request);

        return $view->render($response, 'books/add.twig', $vars);
    }

    public function elenco(Request $request, Response $response): Response
    {
        ob_start(); ?>
    <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
    <HTML>
    <HEAD>
        <META content="text/html; charset=utf-8" http-equiv=Content-Type>
        <link href="css/main.css" rel="stylesheet" type="text/css">
    </HEAD>
    <BODY>
        <?php
        $dbal = $this->getDoctrineConnection();

        $count = $dbal->fetchOne('SELECT COUNT(*) FROM books');

        $offset = $request->getQueryParams()['offset'] ?? 0;
        if (is_string($offset) && preg_match('/^\\d+$/', $offset)) {
            $offset = intval($offset);
        } else {
            $offset = 0;
        }

        $books = $dbal->executeQuery(
            'SELECT ISBN FROM books LIMIT ?, 50',
            [$offset],
            [Type::getType('integer')],
        );

        echo "<table border=\"1\" align=\"center\" width=\"755\">\n";
        echo "<tr>\n";
        echo "<td>ISBN</td>\n";
        echo "<td>Titolo</td>\n";
        echo "<td>Autore</td>\n";
        echo "<td>Editore</td>\n";
        echo "<td>Prezzo</td>\n";
        echo '</tr>', PHP_EOL;

        foreach ($books as $row) {
            echo "<tr>\n";

            $book = new Book($dbal);
            $book->getFromDB($row['ISBN']);

            foreach ($book->getFields() ?: [] as $chiave => $valore) {
                if (!is_numeric($chiave)) {
                    echo "<td>$valore</td>";
                }
            }

            echo '</tr>';
        }
        echo '</table>';

        echo '<a href="/books?offset=' . ($offset + 50) . '">Pagina ' . ($offset / 50 + 2) . '</a>'; ?>
    <p><?php echo $count; ?> libri presenti.</p>
    </BODY>
    </HTML>
<?php
        $response->getBody()->write((string) ob_get_clean());

        return $response;
    }
}
