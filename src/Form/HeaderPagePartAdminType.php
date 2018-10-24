<?php

namespace Kunstmaan\PagePartBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * HeaderPagePartAdminType.
 */
class HeaderPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add(
          'niv',
          ChoiceType::class,
          [
            'label' => 'pagepart.header.type',
            'choices' => ['Header 1' => '1', 'Header 2' => '2', 'Header 3' => '3', 'Header 4' => '4', 'Header 5' => '5', 'Header 6' => '6'],
            'required' => true,
          ]
        );
        $builder->add('title', TextType::class, [
            'label' => 'pagepart.header.title',
            'required' => true,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'kunstmaan_pagepartbundle_headerpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
          [
            'data_class' => 'Kunstmaan\PagePartBundle\Entity\HeaderPagePart',
          ]
        );
    }
}
