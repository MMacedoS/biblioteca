<?php

declare(strict_types=1);

namespace App\Form;

use App\DTO\LivroData;
use App\Entity\Assunto;
use App\Entity\Autor;
use App\Repository\Contract\AssuntoRepositoryInterface;
use App\Repository\Contract\AutorRepositoryInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class LivroType extends AbstractType
{
    public function __construct(
        private readonly AutorRepositoryInterface $autorRepository,
        private readonly AssuntoRepositoryInterface $assuntoRepository,
    ) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo', TextType::class, [
                'label' => 'Título',
                'attr' => ['maxlength' => 40],
            ])
            ->add('editora', TextType::class, [
                'label' => 'Editora',
                'attr' => ['maxlength' => 40],
            ])
            ->add('edicao', IntegerType::class, [
                'label' => 'Edição',
                'attr' => ['min' => 1],
            ])
            ->add('anoPublicacao', TextType::class, [
                'label' => 'Ano de publicação',
                'attr' => [
                    'maxlength' => 4,
                    'inputmode' => 'numeric',
                    'placeholder' => 'Ex.: 1899',
                ],
            ])
            ->add('valor', MoneyType::class, [
                'label' => 'Valor',
                'currency' => 'BRL',
                'attr' => ['placeholder' => '0,00'],
            ])
            ->add('autores', EntityType::class, [
                'label' => 'Autores',
                'class' => Autor::class,
                'choices' => $this->autorRepository->listarTodos(),
                'choice_label' => 'nome',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('assuntos', EntityType::class, [
                'label' => 'Assuntos',
                'class' => Assunto::class,
                'choices' => $this->assuntoRepository->listarTodos(),
                'choice_label' => 'descricao',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => LivroData::class,
        ]);
    }
}
