<?php

namespace App\Form;

use App\Form\Model\CustomShortUrlModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class CustomShortUrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('longUrl', TextareaType::class, [
                'attr' => [
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'autofocus' => 'autofocus',
                    'placeholder' => 'placeholder.enter_url',
                ],
            ])
            ->add('shortUrl', TextType::class, [
                'attr' => [
                    'required' => false,
                    'class' => 'form-control',
                    'autocomplete' => 'off',
                    'placeholder' => 'placeholder.enter_short_code',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => CustomShortUrlModel::class,
        ]);
    }
}
