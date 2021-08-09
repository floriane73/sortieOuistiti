<?php


namespace App\Form;

use App\Data\FiltresData;
use App\Entity\Campus;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FiltresType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('q', TextType::class, [
            'label' => false,
            'required' => false,
            'attr' => [
                'placeholder' => 'Recherche par nom'
            ]
        ])
            ->add('campus', EntityType::class, [
                'label' => 'Campus',
                'required' => false,
                'class' => Campus::class,
                'choice_label' => 'nom'
            ])
            ->add('dateMin', DateType::class, [
                'label' => 'Après le ',
                'required' => false,
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('dateMax', DateType::class, [
                'label' => 'Avant le ',
                'required' => false,
                'html5' => true,
                'widget' => 'single_text'
            ])
            ->add('isOrganisateur', CheckboxType::class, [
                'label' => 'J\'organise',
                'required' => false
            ])
            ->add('isParticipant', CheckboxType::class, [
                'label' => 'Inscrit',
                'required' => false
            ])
            ->add('isNotParticipant', CheckboxType::class, [
                'label' => 'Non inscrit',
                'required' => false
            ])
            ->add('isSortiePassee', CheckboxType::class, [
                'label' => 'Terminées',
                'required' => false
            ]);

    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FiltresData::class,
            'method' => 'GET',
            'csrf_protection' => false,
            'allow_extra_fields' => true
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }

}