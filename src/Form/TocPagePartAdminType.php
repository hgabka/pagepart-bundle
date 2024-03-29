<?php

namespace Hgabka\PagePartBundle\Form;

use Hgabka\PagePartBundle\Entity\TocPagePart;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TocPagePartAdminType.
 */
class TocPagePartAdminType extends AbstractPagePartAdminType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'hgabka_pagepartbundle_tocpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => TocPagePart::class,
        ]);
    }
}
