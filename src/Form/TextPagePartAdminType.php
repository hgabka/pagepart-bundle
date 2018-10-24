<?php

namespace Hgabka\PagePartBundle\Form;

use Hgabka\AdminBundle\Form\WysiwygType;
use Hgabka\PagePartBundle\Entity\TextPagePart;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TextPagePartAdminType.
 */
class TextPagePartAdminType extends AbstractType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('content', WysiwygType::class, [
            'label' => 'pagepart.text.content',
            'required' => false,
        ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'hgabka_pagepartbundle_textpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => TextPagePart::class,
        ]);
    }
}
