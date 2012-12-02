<?php

use Behat\Behat\Exception\PendingException;
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
    }

    /**
     * @Then /^I should get a success message$/
     */
    public function iShouldGetASuccessMessage()
    {
        $this->assertPageContainsText('Libro inserito');
    }
}
