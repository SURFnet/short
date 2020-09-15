<?php

namespace App\Validator;

use App\Exception\ShortUserException;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class NotBannedDomainValidator extends ConstraintValidator
{
    /**
     * @var ParameterBagInterface
     */
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }

    public function validate($value, Constraint $constraint)
    {
        /* @var $constraint NotBannedDomain */

        if (null === $value || '' === $value) {
            return;
        }

        $bannedDomains = $this->parameterBag->get('app.targeturl.forbiddendomains');
        // Also don't want people to create links pointing to ourselves.
        $bannedDomains[] = $this->parameterBag->get('app.urldomain');

        $hostname = parse_url($value, PHP_URL_HOST);

        if ($bannedDomain = array_search(strtolower($hostname), $bannedDomains, TRUE)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('{{ value }}', $bannedDomains[$bannedDomain])
                ->addViolation();
        }
    }
}
