<?php

namespace App\Form;

use App\Entity\ShortUrl;
use App\Message\CreateShortUrl;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

final class ShortUrlType extends AbstractType
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface  $translator)
    {
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('longUrl', UrlType::class, [
            'default_protocol' => null,
            'attr' => [
                'class' => 'form-control',
                'autocomplete' => 'off',
                'autofocus' => 'autofocus',
                'placeholder' => $this->translator->trans('placeholder.enter_url'),
            ],
        ]);

        if ($options['is_admin']) {
            $builder->add('shortUrl', TextType::class, [
                'attr' => [
                    'required' => false,
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'placeholder' => $this->translator->trans('placeholder.enter_short_code'),
                ],
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShortUrl::class,
            'is_admin' => false,
        ]);

        $resolver->setAllowedTypes('is_admin', 'bool');
    }
}
