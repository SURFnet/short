<?php


namespace App\Tests\Behat\Page;


use App\Entity\ShortUrl;
use Behat\Mink\Element\NodeElement;
use Behat\Mink\Exception\ElementNotFoundException;
use FriendsOfBehat\PageObjectExtension\Page\SymfonyPage;
use function Symfony\Component\String\u;

final class AdminShortUrlIndexPage extends SymfonyPage
{
    public function getRouteName(): string
    {
        return 'app_manage_admin';
    }

    public function specifyLongUrl(string $longUrl): void
    {
        $this->getDocument()->fillField('Enter the original link (URL) here', $longUrl);
    }

    public function specifyCode(string $code): void
    {
        $this->getDocument()->fillField('The desired short code (empty for autogenerate)', $code);
    }

    public function shortIt(): void
    {
        $this->getDocument()->pressButton('Shorten it!');
    }

    public function countLinks(): int
    {
        return count($this->getElement('links')->findAll('css', 'tr'));
    }

    public function getNotificationMessage(): string
    {
        $notificationMessage = $this->getDocument()->find('css', '.alert');
        if (null === $notificationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Notification message', 'css', '.alert');
        }

        return $notificationMessage->getText();

    }

    public function getErrorMessage(): string
    {
        $validationMessage = $this->getDocument()->find('css', '.form-error-message');
        if (null === $validationMessage) {
            throw new ElementNotFoundException($this->getSession(), 'Validation message', 'css', '.form-error-message');
        }

        return $validationMessage->getText();
    }

    public function deleteShortUrl(ShortUrl $shortUrl)
    {
        $rows = $this->getDocument()->findAll('css', 'tr');

        foreach ($rows as $row) {
            $column = $row->find('css', 'td:nth-child(5)');
            if ($column && $column->getText() === $shortUrl->getLongUrl()) {
                $row->find('css', 'tr > td > form > button')->click();
                return;
            }
        }

        throw new \Exception('Short Url not found');
    }

    public function isDeleted(ShortUrl $shortUrl)
    {
        $row = $this->getShortUrlRow($shortUrl);
        if ($row) {
            $column = $row->find('css', 'td:nth-child(5)');
            return $column->hasClass('text-truncate');
        }

        throw new \Exception('Short Url not found');
    }

    public function getLongUrl(ShortUrl $shortUrl)
    {
        $row = $this->getShortUrlRow($shortUrl);
        if ($row) {
            return $row->find('css', 'td:nth-child(5)')->getText();
        }

        throw new \Exception('Short Url not found');
    }

    public function shortUrlExists(ShortUrl $shortUrl): bool
    {
        $row = $this->getShortUrlRow($shortUrl);

        return $row instanceof NodeElement;
    }

    protected function getShortUrlRow(ShortUrl $shortUrl): ?NodeElement
    {
        $rows = $this->getDocument()->findAll('css', 'tr');

        foreach ($rows as $row) {
            $column = $row->find('css', 'td:nth-child(3)');
            if (!$column) continue;

            $text = u($column->getText());
            if ($text->endsWith($shortUrl->getShortUrl())) {
                return $row;
            }
        }

        return null;
    }


    protected function getDefinedElements(): array
    {
        return array_merge(parent::getDefinedElements(), [
            'links' => '[data-test=links]',
        ]);
    }
}
