<?php


namespace App\DependencyInjection\Compiler;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Exception\RuntimeException;
use Symfony\Component\DependencyInjection\Reference;

class ApacheModAuthGuardPass implements CompilerPassInterface
{
    private const GUARD_SERVICE_CLASS = 'App\Security\Guard\ApacheModAuth%sGuardAuthenticator';

    public function process(ContainerBuilder $container)
    {
        if ($container->has('app_guard_authenticator')) {
            return;
        }

        $guardServiceType = $container->resolveEnvPlaceholders($container->getParameter('app.security'), true);
        $guardServiceClass = sprintf(self::GUARD_SERVICE_CLASS, ucfirst($guardServiceType));

        if (!class_exists($guardServiceClass)) {
            throw new RuntimeException(sprintf('Invalid security type: "%s". Configure APP_MOD_SECURITY environment variable as "mellon" or "openidc"', $guardServiceType));
        }

        $guardService = new Definition($guardServiceClass, [new Reference('router'), new Reference('parameter_bag')]);
        $guardService->setAutowired(true);
        $guardService->setAutoconfigured(true);

        $container->setDefinition('app_guard_authenticator', $guardService);
    }
}
