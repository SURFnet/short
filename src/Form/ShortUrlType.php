<?php

namespace App\Form;

use App\Form\Model\ShortUrlModel;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class ShortUrlType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('longUrl', TextareaType::class, [
            'attr' => [
                'class' => 'form-control',
                'autocomplete' => 'off',
                'autofocus' => 'autofocus',
                'placeholder' => 'placeholder.enter_url',
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
