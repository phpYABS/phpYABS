<?php

use Behat\MinkExtension\Context\MinkContext;

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
    private $homeurl = 'http://www.phpyabs.local/';

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

        $this->fields[$arg2] = $arg1;

        $session = $this->getSession();
        $page = $session->getPage();

        $page->fillField($fields[$arg2], $arg1);
    }

    /**
     * @Given /^I click on "([^"]*)" button$/
     */
    public function iClickOnButton($arg1)
    {
        $translate = array (
            'Add' => 'Aggiungi'
        );

        $button = $translate[$arg1];

        $this
            ->getSession()
            ->getPage()
            ->pressButton($button)
        ;

        $this->getSession()->wait(200);
    }

    /**
     * @Then /^I should get a success message$/
     */
    public function iShouldGetASuccessMessage()
    {
        $this->assertPageContainsText('Libro inserito');
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
}
