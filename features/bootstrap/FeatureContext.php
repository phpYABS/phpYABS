<?php

use Behat\MinkExtension\Context\MinkContext;
use Behat\Behat\Exception\PendingException;
use Behat\Behat\Exception\UndefinedException;

//
// Require 3rd-party libraries here:
//
//   require_once 'PHPUnit/Autoload.php';
//   require_once 'PHPUnit/Framework/Assert/Functions.php';
//

require_once __DIR__ .'/../../web/application/includes/common.inc.php';

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
        $session->visit('http://www.phpyabs.local/modules.php?Nome=Libri&Azione=Aggiungi');
    }


    /**
     * @When /^I type "([^"]*)" on "([^"]*)" field$/
     */
    public function iTypeOnField($arg1, $arg2)
    {
        $fields = array (
            'ISBN' => 'ISBN',
            'title' => 'Titolo',
            'author' => 'Autore',
            'publisher' => 'Editore',
            'price'     => 'Prezzo'
        );

        $actualField = array_key_exists($arg2, $fields) ? $fields[$arg2] : $arg2;

        $this->fields[$arg2] = $arg1;

        $session = $this->getSession();
        $page = $session->getPage();

        $page->fillField($actualField, $arg1);
    }

    /**
     * @Given /^I click on "([^"]*)" button$/
     */
    public function iClickOnButton($buttonName)
    {
        $translate = array (
            'Add' => 'Aggiungi'
        );

        $button = array_key_exists($buttonName, $translate) ? $translate[$buttonName] : $buttonName;

        $this
            ->getSession()
            ->getPage()
            ->pressButton($button)
        ;

        $this->getSession()->wait(200);
    }

    /**
     * @Then /^Text "([^"]*)" should be present$/
     */
    public function textShouldBePresent($message)
    {
        $translations = array(
            'book successfully added' => 'Libro inserito',
            'book successfully deleted' => 'Libro Cancellato!',
            '0 books purchased' => '0 Libri acquistati'
        );

        $actualMessage = array_key_exists($message, $translations) ? $translations[$message] : $message;

        $this->assertPageContainsText($actualMessage);
    }


    /**
     * @Given /^I am on book list page$/
     */
    public function iAmOnBookListPage()
    {
        $session = $this->getSession();
        $session->visit('http://www.phpyabs.local/modules.php?Nome=Libri&Azione=Elenco');
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
     * @Given /^I select "([^"]*)" on field "([^"]*)"$/
     */
    public function iSelectOnField($arg1, $arg2)
    {
        $this->getSession()->getPage()->selectFieldOption($arg2, $arg1);
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
     * @Given /^I am on book delete page$/
     */
    public function iAmOnBookDeletePage()
    {
        $session = $this->getSession();
        $session->visit('http://www.phpyabs.local/modules.php?Nome=Libri&Azione=Cancella');
    }

    /**
     * @Given /^I click on "([^"]*)" link$/
     */
    public function iClickOnLink($buttonName)
    {
        $translate = array (
            'Delete Book' => 'Cancella Libro'
        );

        $button = array_key_exists($buttonName, $translate) ? $translate[$buttonName] : $buttonName;

        $this
            ->getSession()
            ->getPage()
            ->clickLink($button)
        ;

        $this->getSession()->wait(200);
    }

    /**
     * @Then /^I should see no book$/
     */
    public function iShouldSeeNoBook()
    {
        $this->assertPageContainsText('0 libri presenti');
    }

    /**
     * @Given /^I am on "([^"]*)" page$/
     */
    public function iAmOnPage($pageName)
    {
        $session = $this->getSession();

        $pages = array ('new cart' => 'http://www.phpyabs.local/modules.php?Nome=Acquisti&Azione=Nuovo');

        if(!array_key_exists($pageName, $pages)) {
            throw new UndefinedException('Page not defined');
        }

        $session->visit($pages[$pageName]);
    }
}
