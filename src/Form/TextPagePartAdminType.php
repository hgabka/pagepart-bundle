<?php

namespace Hgabka\PagePartBundle\Form;

use Hgabka\PagePartBundle\Entity\TextPagePart;
use Hgabka\UtilsBundle\Form\WysiwygType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * TextPagePartAdminType.
 */
class TextPagePartAdminType extends AbstractPagePartAdminType
{
    /**
     * @param \Symfony\Component\Form\FormBuilderInterface $builder
     * @param array                                        $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $params = [
            'label' => 'pagepart.text.content',
            'required' => false,
            'attr' => [
                'class' => 'form-control',
            ],
        ];
        if (isset($options['config']['editorCss'])) {
            $params['config'] = [
                'contentsCss' => $options['config']['editorCss'],
            ];
        }

        if (isset($options['config']['editor_mode'])) {
            $params['attr']['type'] = $options['config']['editor_mode'];
        }

        $builder->add('content', WysiwygType::class, $params);
    }

    /**
     * @return string
     */
    public function getBlockPrefix(): string
    {
        return 'hgabka_pagepartbundle_textpageparttype';
    }

    /**
     * @param \Symfony\Component\OptionsResolver\OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => TextPagePart::class,
        ]);
    }
}
