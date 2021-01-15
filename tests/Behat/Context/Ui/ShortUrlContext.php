<?php


namespace App\Tests\Behat\Context\Ui;


use App\Tests\Behat\Page\ShortUrlIndexPage;
use Behat\Behat\Context\Context;
use Webmozart\Assert\Assert;

final class ShortUrlContext implements Context
{
    /**
     * @var ShortUrlIndexPage
     */
    private $shortUrlIndexPage;

    public function __construct(ShortUrlIndexPage $shortUrlIndexPage)
    {
        $this->shortUrlIndexPage = $shortUrlIndexPage;
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

}
