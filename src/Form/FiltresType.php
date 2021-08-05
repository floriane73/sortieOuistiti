<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltresType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('campus', EntityType::class,[
                'class'=>Campus::class,
                'choice_label' => 'nom',
                ])
            ->add('nom', TextType::class,[
                    'label' => 'Le nom de la sortie contient',
                    'mapped'=> false
                ])

            ->add('dateDebut', DateType::class,[

                'mapped'=> false
            ])
            ->add('dateFin', DateType::class,[

                'mapped'=> false
            ])

            ->add('organisateur', CheckboxType::class,[
                'label'=> 'Sorties dont je rties dont je suis l\'organisateur/trice',

                'mapped'=>false
            ])
            ->add('inscrit', CheckboxType::class,[
            'label'=> 'Sorties dont je rties dont je suis',
                'mapped'=>false
            ])
            ->add('pasInscrit', CheckboxType::class,[
                'label'=> 'Sorties dont je rties dont je suis',
                'mapped'=>false
            ])
            ->add('passee', CheckboxType::class,[
                'label'=> 'Sorties dont je rties dont je suis',
                'mapped'=>false
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
