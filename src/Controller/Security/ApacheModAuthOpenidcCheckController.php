<?php


namespace App\Controller\Security;

use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/connect/inacademia/validate", name="connect_openidc_check")
 */
class ApacheModAuthOpenidcCheckController extends AbstractController
{
    public function __invoke(): void
    {
        throw new RuntimeException('This method should not be called.');
    }
}
