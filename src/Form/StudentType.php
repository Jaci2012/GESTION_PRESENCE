<?php

namespace App\Form;

use App\Entity\Level;
use App\Entity\Career;
use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('matricule')
            ->add('lastname', null, [
                'label' => 'Nom'
            ])
            ->add('firstname', null, [
                'label' => 'Prénom(s)'
            ])
            ->add('adresse')
            ->add('career', EntityType::class, [
                'class' => Career::class,
                'choice_label' => 'design',
                // La propriété de Category à afficher dans la liste déroulante
                'placeholder' => 'Sélectionnez un parcours',
                // Texte par défaut
            ])
            ->add('years', null, [
                'label' => 'Année universitaire'
            ])
            ->add('level', EntityType::class, [
                'class' => Level::class,
                'choice_label' => 'design',
                // La propriété de Category à afficher dans la liste déroulante
                'placeholder' => 'Sélectionnez un niveau',
                // Texte par défaut
            ])
            ->add('photos', FileType::class, [
                'label' => 'Télécharger une photo',
                'required' => false
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}