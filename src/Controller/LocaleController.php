<?php


namespace App\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

/**
 * @Route("/locale/{lang}", name="locale")
 */
final class LocaleController extends AbstractController
{
    use TargetPathTrait;

    /**
     * @var array
     */
    private $locales;
    /**
     * @var string
     */
    private $defaultLocale;

    public function __construct(array $locales, string $defaultLocale)
    {
        $this->locales = $locales;
        $this->defaultLocale = $defaultLocale;
    }

    public function __invoke(Request $request, string $lang)
    {
        if (!in_array($lang, $this->locales)) {
            $lang = $this->defaultLocale;
        }

        $request->getSession()->set('_locale', $lang);

        if ($redirectUrl = $this->getRedirectUrl($request)) {
            return $this->redirect($redirectUrl);
        }

        return $this->redirectToRoute('app_info_index');
    }

    private function getRedirectUrl(Request $request): ?string
    {
        if (!$request->query->has('redirect')) {
            return null;
        }

        $redirectUrl = $request->query->get('redirect');

        // Prevent open redirect: URL must start with slash and then only alnum,
        // only later maybe further slashes
        if (!preg_match('#^/[a-zA-Z0-9][a-zA-Z0-9/]+$#', $redirectUrl)) {
            return null;
        }

        return $redirectUrl;
    }
}
