<?php

namespace App\Form;

use App\Entity\Curso;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class RegisterCursoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('titulo',TextType::class)
            ->add('descripcion',TextType::class)
        ;
            #->add('estado')
            #->add('userId')
        if($options['accion']== 'editCurso')
        {
            $builder -> add('estado', ChoiceType:: class, array(
                'choices' => array(
                    'En Desarrollo' => 'EnDesarrollo',
                    'Activo' => 'Activo',
                    'Inactivo' => 'Inactivo',
                ) 
                ));
        }
        $builder 
            ->add('save', SubmitType :: class, ['label' => 'Guardar']);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Curso::class,
            'accion' => 'crearJuego'
        ]);
    }
}
