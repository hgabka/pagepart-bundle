<?php

namespace Hgabka\PagePartBundle\Form;

use Hgabka\NodeBundle\Form\Type\URLChooserType;
use Hgabka\PagePartBundle\Entity\LinkPagePart;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * LinkPagePartAdminType.
 */
class LinkPagePartAdminType extends AbstractPagePartAdminType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('url', URLChooserType::class, [
                'label' => false,
                'required' => false,
            ])
            ->add('openinnewwindow', CheckboxType::class, [
                'label' => 'pagepart.link.openinnewwindow',
                'required' => false,
            ])
            ->add('text', TextType::class, [
                'label' => 'pagepart.link.text',
                'required' => false,
            ]);
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'hgabka_pagepartbundle_linkpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => LinkPagePart::class,
        ]);
    }
}
