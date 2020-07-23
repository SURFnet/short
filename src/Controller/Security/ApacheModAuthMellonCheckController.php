<?php


namespace App\Controller\Security;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ApacheModAuthMellonCheckController extends AbstractController
{
    public function __invoke(): void
    {
        throw new RuntimeException('This method should not be called.');
    }
}
