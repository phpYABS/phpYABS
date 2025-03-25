<?php

declare(strict_types=1);

namespace PhpYabs\Tests\Behat;

use Behat\MinkExtension\Context\MinkContext;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use PhpYabs\Entity\Book;
use PhpYabs\Entity\Rate;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * This context class contains the definitions of the steps used by the demo
 * feature file. Learn how to get started with Behat and BDD on Behat's website.
 *
 * @see http://behat.org/en/latest/quick_start.html
 */
final class MainContext extends MinkContext
{
    private ?Response $response;

    private Connection $dbal;

    public function __construct(
        private readonly KernelInterface $kernel,
        private readonly EntityManagerInterface $entityManager,
    ) {
        $this->dbal = $this->entityManager->getConnection();
    }

    /**
     * @When a demo scenario sends a request to :path
     */
    public function aDemoScenarioSendsARequestTo(string $path): void
    {
        $this->response = $this->kernel->handle(Request::create($path, 'GET'));
    }

    /**
     * @Then the response should be received
     */
    public function theResponseShouldBeReceived(): void
    {
        if (null === $this->response) {
            throw new \RuntimeException('No response received');
        }
    }

    /**
     * @Given /^there is no book in database$/
     */
    public function thereIsNoBookInDatabase()
    {
        $this->dbal->executeStatement('DELETE FROM books');
    }

    /**
     * @Given /^a book with ISBN "([^"]*)" and title "([^"]*)" and author "([^"]*)" and publisher "([^"]*)" and price "([^"]*)" and valutazione "([^"]*)" is added$/
     */
    public function aBookIsAdded(string $ISBN, string $title, string $author, string $publisher, string $price, string $rate)
    {
        $book = new Book();
        $book->setIsbn($ISBN)
            ->setTitle($title)
            ->setAuthor($author)
            ->setPublisher($publisher)
            ->setPrice($price)
            ->setRate(Rate::tryFrom($rate))
        ;

        $this->entityManager->persist($book);
        $this->entityManager->flush();
    }
}
