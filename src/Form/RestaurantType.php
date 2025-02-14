<?php

namespace App\Form;

use App\Entity\Restaurant;
use App\Entity\Type;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RestaurantType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('description')
            ->add('logoFile', FileType::class, [
                'label' => "Choisissez une image",
                'mapped' => false
              ])
            ->add('address')
            ->add('postal_code')
            ->add('city')
            ->add('email')
            ->add('type', EntityType::class, [
                'class' => Type::class,
                'choice_label' => 'name',
            ])
            ->add('balance')
            //user_id
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Restaurant::class,
        ]);
    }
}
