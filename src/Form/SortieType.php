<?php

namespace App\Form;

use App\Entity\Campus;
use App\Entity\Lieu;
use App\Entity\Sortie;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SortieType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('dateHeureDebut', DateTimeType::class, [
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('duree', NumberType::class)
            ->add('dateLimiteInscription', DateType::class, [
                'html5'=>true,
                'widget'=>'single_text'
            ])
            ->add('nbInscriptionsMax', NumberType::class)
            ->add('description', TextareaType::class)
            ->add('lieu', EntityType::class, [
                'label'=>'Lieu',
                'class'=>Lieu::class,
                'choice_label'=>'nom'
            ])
            /*->add('campus', EntityType::class, [
                'label'=>'Campus',
                'class'=>Campus::class,
                'choice_label'=>'nom'
            ])*/
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Sortie::class,
        ]);
    }
}
