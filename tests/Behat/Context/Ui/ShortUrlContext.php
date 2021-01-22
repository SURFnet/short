<?php


namespace App\Tests\Behat\Context\Ui;


use App\Entity\ShortUrl;
use App\Tests\Behat\Page\AdminShortUrlIndexPage;
use App\Tests\Behat\Page\ShortUrlIndexPage;
use App\Tests\Behat\Page\ShortUrlUpdatePage;
use App\Tests\Behat\Service\SharedStorage;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

final class ShortUrlContext implements Context
{
    /**
     * @var SharedStorage
     */
    private $sharedStorage;
    /**
     * @var ShortUrlIndexPage
     */
    private $shortUrlIndexPage;
    /**
     * @var ShortUrlUpdatePage
     */
    private $shortUrlUpdatePage;
    /**
     * @var AdminShortUrlIndexPage
     */
    private $adminShortUrlIndexPage;

    public function __construct(
        SharedStorage $sharedStorage,
        ShortUrlIndexPage $shortUrlIndexPage,
        ShortUrlUpdatePage $shortUrlUpdatePage,
        AdminShortUrlIndexPage $adminShortUrlIndexPage
    ) {
        $this->sharedStorage = $sharedStorage;
        $this->shortUrlIndexPage = $shortUrlIndexPage;
        $this->shortUrlUpdatePage = $shortUrlUpdatePage;
        $this->adminShortUrlIndexPage = $adminShortUrlIndexPage;
    }

    /**
     * @When /^I want to generate a new short url$/
     */
    public function iWantToGenerateANewShortUrl()
    {
        $this->shortUrlIndexPage->open();
    }

    /**
     * @Given /^I write the long url "([^"]*)"$/
     */
    public function iWriteTheLongUrl(string $longUrl)
    {
        $this->shortUrlIndexPage->specifyLongUrl($longUrl);
    }

    /**
     * @Given /^I short it$/
     */
    public function iShortIt()
    {
        $this->shortUrlIndexPage->shortIt();
    }

    /**
     * @Then /^I should see (\d+) shortened urls on my list$/
     */
    public function iShouldSeeShortenedUrlsOnMyList(int $count)
    {
        $this->shortUrlIndexPage->open();

        Assert::same($this->shortUrlIndexPage->countLinks(), $count);
    }

    /**
     * @Then /^I should see the URL should not be blank$/
     */
    public function iShouldSeeTheURLShouldNotBeBlank()
    {
        Assert::same($this->shortUrlIndexPage->getErrorMessage(), 'This value should not be blank.');
    }

    /**
     * @Then /^I should see the URL is invalid$/
     */
    public function iShouldSeeTheURLIsInvalid()
    {
        Assert::startsWith($this->shortUrlIndexPage->getErrorMessage(), 'Oops, this is not quite a correct URL,');
    }

    /**
     * @Then /^I should see that target URL may not start with "([^"]*)"$/
     */
    public function iShouldSeeThatTargetURLMayNotStartWith(string $domain)
    {
        $message = sprintf('Target URL may not start with "%s".', $domain);

        Assert::same($this->shortUrlIndexPage->getErrorMessage(), $message);
    }

    /**
     * @When I delete the short url for :shortUrl
     */
    public function iDeleteTheShortUrlFor(ShortUrl $shortUrl)
    {
        $this->sharedStorage->set('short_url', $shortUrl);
        $this->shortUrlIndexPage->open();
        $this->shortUrlIndexPage->deleteShortUrl($shortUrl);
    }

    /**
     * @Then /^I should be notified that it has been successfully deleted$/
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyDeleted()
    {
        Assert::startsWith(
            $this->shortUrlIndexPage->getNotificationMessage(),
            "The URL was deleted."
        );
    }

    /**
     * @Given /^(it) should appears as deleted in the list$/
     */
    public function thisShortUrlShouldAppearsAsDeletedInTheList(ShortUrl $shortUrl)
    {
        Assert::true($this->shortUrlIndexPage->isDeleted($shortUrl));
    }

    /**
     * @Given I want to modify the short url for :shortUrl
     */
    public function iWantToModifyTheLongUrl(ShortUrl $shortUrl)
    {
        $this->sharedStorage->set('short_url', $shortUrl);

        $this->shortUrlUpdatePage->open(['shortUrl' => $shortUrl->getShortUrl()]);
    }

    /**
     * @When /^I update it with "([^"]*)"$/
     */
    public function iUpdateItWith(string $longUrl)
    {
        $this->shortUrlUpdatePage->updateUrl($longUrl);
    }

    /**
     * @Given /^I save the changes$/
     */
    public function iSaveTheChanges()
    {
        $this->shortUrlUpdatePage->modify();
    }

    /**
     * @Then /^I should be notified that it has been successfully updated$/
     */
    public function iShouldBeNotifiedThatItHasBeenSuccessfullyUpdated()
    {
        Assert::startsWith(
            $this->shortUrlIndexPage->getNotificationMessage(),
            "The URL was updated."
        );
    }

    /**
     * @Given /^(it) should redirect to "([^"]*)"$/
     */
    public function thisShortUrlShouldRedirectTo(ShortUrl $shortUrl, string $longUrl)
    {
        Assert::eq(
            $this->shortUrlIndexPage->getLongUrl($shortUrl),
            $longUrl
        );
    }

    /**
     * @When /^I want to generate a new custom short url$/
     */
    public function iWantToGenerateANewCustomShortUrl()
    {
        $this->adminShortUrlIndexPage->open();
    }

    /**
     * @Given /^I write the short code "([^"]*)"$/
     */
    public function iWriteTheShortCode(string $code)
    {
        $this->adminShortUrlIndexPage->specifyCode($code);
    }

    /**
     * @Then the short url with code (:code) should be on my list
     */
    public function theShortUrlShouldBeOnMyList(ShortUrl $shortUrl)
    {
        Assert::true($this->adminShortUrlIndexPage->shortUrlExists($shortUrl));
    }
}
