<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Mink\Driver\Selenium2Driver;
use Behat\Mink\Exception\UnsupportedDriverActionException;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Exception\UndefinedException;


//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

require_once __DIR__ .'/../../application/includes/common.inc.php';

/**
 * Features context.
 */
class FeatureContext extends MinkContext
{
     private $fields = array();

    /**
     * Initializes context.
     * Every scenario gets it's own context object.
     *
     * @param array $parameters context parameters (set them up through behat.yml)
     */
    public function __construct(array $parameters)
    {
    }

    /**
     * @Given /^there is no book in database$/
     */
    public function thereIsNoBookInDatabase()
    {
        global $conn;
        $conn->Execute('DELETE FROM phpyabs_libri');
    }

    /**
     * @Given /^I am on book add page$/
     */
    public function iAmOnBookAddPage()
    {
        $session = $this->getSession();
        $session->visit('http://www.phpyabs.dev/modules.php?Nome=Libri&Azione=Aggiungi');
    }


    /**
     * @Then /^I should see book fields$/
     */
    public function iShouldSeeBookFields()
    {
        foreach ($this->fields as $value) {
            $this->assertPageContainsText($value);
        }
    }

    /**
     * @Given /^a book with ISBN "([^"]*)" and title "([^"]*)" and author "([^"]*)" and publisher "([^"]*)" and price "([^"]*)" and valutazione "([^"]*)" is added$/
     */
    public function aBookWithIsbnAndTitleAndAuthorAndPublisherAndPriceAndValutazioneIsAdded($isbn, $title, $author, $publisher, $price, $valutazione)
    {
        $book = new PhpYabs_Book();

        $book->setFields(array(
            'ISBN' => $isbn,
            'Titolo' => $title,
            'Autore' => $author,
            'Editore' => $publisher,
            'Prezzo' => $price,
            'Valutazione' => $valutazione
        ));

        $book->saveToDB();
    }

    /**
     * @Given /^I am on "([^"]*)" page$/
     */
    public function iAmOnPage($pageName)
    {
        $session = $this->getSession();

        $pages = array (
            'new cart' => 'http://www.phpyabs.dev/modules.php?Nome=Acquisti&Azione=Nuovo',
            'delete book' => 'http://www.phpyabs.dev/modules.php?Nome=Libri&Azione=Cancella',
            'edit book' => 'http://www.phpyabs.dev/modules.php?Nome=Libri&Azione=Modifica',
            'book list' =>'http://www.phpyabs.dev/modules.php?Nome=Libri&Azione=Elenco',
            'add book' =>'http://www.phpyabs.dev/modules.php?Nome=Libri&Azione=Aggiungi',
        );

        if(!array_key_exists($pageName, $pages)) {
            throw new PendingException('Page not defined');
        }

        $session->visit($pages[$pageName]);
    }

    /**
     * Take screenshot when step fails.
     * Works only with Selenium2Driver.
     *
     * @AfterStep
     */
    public function takeScreenshotAfterFailedStep($event)
    {
        if (4 === $event->getResult()) {
            $driver = $this->getSession()->getDriver();

            if (!$driver instanceof Selenium2Driver) {
                throw new UnsupportedDriverActionException('Taking screenshots is not supported by %s, use Selenium2Driver instead.', $driver);
            }

            file_put_contents(__DIR__ . '/../failure.png', $driver->getScreenshot());
        }
    }
}
