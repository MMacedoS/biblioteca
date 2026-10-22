<?php

declare(strict_types=1);

namespace App\Form;

use App\DTO\AutorData;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class AutorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('nome', TextType::class, [
            'label' => 'Nome',
            'attr' => [
                'maxlength' => 40,
                'placeholder' => 'Ex.: Machado de Assis',
            ],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => AutorData::class,
        ]);
    }
}
