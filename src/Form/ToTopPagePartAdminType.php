<?php

namespace Hgabka\PagePartBundle\Form;

use Hgabka\PagePartBundle\Entity\ToTopPagePart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ToTopPagePartAdminType.
 */
class ToTopPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'hgabka_pagepartbundle_totoppageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ToTopPagePart::class,
        ]);
    }
}
