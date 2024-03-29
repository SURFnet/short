<?php

namespace App\Form;

use App\Form\Model\ShortUrlModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShortUrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('longUrl', UrlType::class, [
            'default_protocol' => null,
            'attr' => [
                'class' => 'form-control',
                'autocomplete' => 'off',
                'autofocus' => 'autofocus',
                'placeholder' => 'placeholder.enter_url',
            ],
        ]);
        $builder->add('label', TextType::class, [
            'required' => false,
            'attr' => [
                'class' => 'form-control',
                'placeholder' => 'placeholder.label',
                'maxlength' => 255,
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ShortUrlModel::class,
        ]);
    }
}
