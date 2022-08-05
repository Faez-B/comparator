<?php

namespace App\Form;

use App\Entity\Marque;
use App\Entity\Voiture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class VoitureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('prix')
            // ->add('marque', ChoiceType::class, [
            //     'placeholder' => 'Choisissez une marque',
            //     'choice_value' => 'nom',
            //     'choice_attr' => function(?Marque $marque) {
            //         return $marque ? ['class' => 'marque_'.strtolower($marque->getName())] : [];
            //     },
            // ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Voiture::class,
        ]);
    }
}
