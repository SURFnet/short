<?php


namespace App\Tests\Behat\Page;


use App\Entity\ShortUrl;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class ShortUrlUpdatePage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'app_manage_edit';
    }

    public function updateUrl(string $longUrl)
    {
        $this->getDocument()->fillField('short_url[longUrl]', $longUrl);
    }

    public function modify()
    {
        $this->getDocument()->pressButton('Modify');
    }
}
