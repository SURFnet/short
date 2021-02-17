<?php


namespace App\Controller\Security;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class InAcademiaCheckController extends AbstractController
{
    public function __invoke(): void
    {
        throw new RuntimeException('This method should not be called.');
    }
}
