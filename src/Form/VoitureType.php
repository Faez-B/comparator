<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Energie;
use App\Entity\Voiture;
use App\Repository\MarqueRepository;
use App\Repository\EnergieRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom de la voiture',
                'attr' => [
                    'placeholder' => 'Nom de la voiture',
                    'autocomplete' => true,
                    'autofocus' => true,
                    'class' => 'px-5 py-3 rounded-full color-gris my-2'
                ]
            ])
            ->add('prix', MoneyType::class, [
                'label' => 'Prix de la voiture',
                'currency' => false,
                'attr' => [
                    'placeholder' => 'Prix de la voiture',
                    'autocomplete' => true,
                    'autofocus' => true,
                    'class' => 'px-5 py-3 rounded-full text-slate-700 my-2 dark:text-slate-700'
                ]
            ])
            ->add('marque', EntityType::class, [
                'class' => Marque::class,
                'choice_label' => 'nom',
                'query_builder' => function (MarqueRepository $repo) {
                    return $repo->createQueryBuilder('m')
                        ->orderBy('m.nom', 'ASC');
                },
                'attr' => [
                    'class' => 'px-5 py-3 dark:text-slate-200 dark:bg-slate-400'
                ]
            ])
            ->add('energie', EntityType::class, [
                'class' => Energie::class,
                'choice_label' => 'nom',
                'query_builder' => function (EnergieRepository $repo) {
                    return $repo->createQueryBuilder('e')
                        ->orderBy('e.nom', 'ASC');
                },
                'attr' => [
                    'class' => 'px-5 py-3 dark:text-slate-200 dark:bg-slate-400'
                ]
            ])
            ->add('etat', ChoiceType::class, [
                'expanded' => true,
                'multiple' => false,
                'choices' => [
                    'Neuf' => 'Neuf',
                    'Occasion' => 'Occasion',
                ],
                'attr' => [
                    'class' => 'etat-change',                    
                ],
            ])
            ->add('consommation', NumberType::class, [
                'attr' => [
                    'placeholder' => 'Consommation de la voiture',
                    'autocomplete' => true,
                    'autofocus' => true,
                    'class' => 'px-5 py-3 rounded-full text-gray-700 my-2 dark:text-gray-700'
                ]
            ])
            ->add('annee', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Année de la voiture',
                    'autocomplete' => true,
                    'autofocus' => true,
                ]
            ])
            ->add('kilometrage', IntegerType::class, [
                'attr' => [
                    'placeholder' => 'Kilométrage de la voiture',
                    'autocomplete' => true,
                    'autofocus' => true,
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
