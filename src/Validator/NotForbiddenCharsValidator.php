<?php

namespace App\Validator;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

class NotForbiddenCharsValidator extends ConstraintValidator
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
        /* @var $constraint NotForbiddenChars */

        $shortcodeForbiddenchars = $this->parameterBag->get('app.shortcode.forbiddenchars');

        if(!preg_match($shortcodeForbiddenchars, $value)) {
            return;
        }

        $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
    }
}
