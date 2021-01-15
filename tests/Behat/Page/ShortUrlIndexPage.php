<?php


namespace App\Tests\Behat\Page;


use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;

final class ShortUrlIndexPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'app_manage_index';
    }

    public function specifyLongUrl(string $longUrl): void
    {
        $this->getDocument()->fillField('Enter the original link (URL) here', $longUrl);
    }

    public function shortIt(): void
    {
        $this->getDocument()->pressButton('Shorten it!');
    }

    public function countLinks(): int
    {
        return count($this->getElement('links')->findAll('css', 'tr'));
    }

    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'links' => '[data-test=links]',
        ]);
    }
}
