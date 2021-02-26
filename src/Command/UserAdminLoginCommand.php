<?php


namespace App\Command;


use App\Component\Messenger\HandleTrait;
use App\Message\User\AddUserMessage;
use App\Repository\InstitutionRepository;
use App\Repository\UserRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;

final class UserAdminLoginCommand extends Command
{
    use HandleTrait;

    /**
     * @var LoginLinkHandlerInterface
     */
    private $mainLoginLinkHandler;
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var InstitutionRepository
     */
    private $institutionRepository;
    /**
     * @var RouterInterface
     */
    private $router;
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(
        MessageBusInterface $messageBus,
        LoginLinkHandlerInterface $mainLoginLinkHandler,
        UserRepository $userRepository,
        RouterInterface $router,
        ParameterBagInterface $parameterBag
    ) {
        parent::__construct();

        $this->messageBus = $messageBus;
        $this->mainLoginLinkHandler = $mainLoginLinkHandler;
        $this->userRepository = $userRepository;
        $this->router = $router;
        $this->parameterBag = $parameterBag;
    }

    protected function configure()
    {
        $this
            ->setName('app:user:login')
            ->setDescription('Creates a link to login as an admin')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $this->configureRouterContext();

        $user = $this->getUser();
        $loginLinkDetails = $this->mainLoginLinkHandler->createLoginLink($user);
        $loginLink = $loginLinkDetails->getUrl();

        $io->success('Open this link to login as an admin user:');
        $output->writeln($loginLink);

        return Command::SUCCESS;
    }

    /**
     * @return \App\Entity\User
     */
    protected function getUser()
    {
        $user = $this->userRepository->find('admin');
        if (!$user) {
            $user = $this->handle(
                new AddUserMessage('admin', ['ROLE_ADMIN'])
            );
        }

        return $user;
    }

    protected function configureRouterContext(): void
    {
        $context = $this->router->getContext();
        $context->setScheme($this->parameterBag->get('app.protocol'));
        $context->setHost($this->parameterBag->get('app.urldomain'));
    }
}
