<?php


namespace App\Controller\Security;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginLinkCheckController extends AbstractController
{
    public function __invoke(): void
    {
        throw new \LogicException('This code should never be reached');
    }
}
