<?php

namespace Hgabka\PagePartBundle\Form;

use Hgabka\MediaBundle\Form\Type\MediaType;
use Hgabka\NodeBundle\Form\Type\URLChooserType;
use Hgabka\PagePartBundle\Entity\ImagePagePart;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ImagePagePartAdminType.
 */
class ImagePagePartAdminType extends AbstractPagePartAdminType
{
    /**
     * Builds the form.
     *
     * This method is called for each type in the hierarchy starting form the
     * top most type. Type extensions can further modify the form.
     *
     * @param FormBuilderInterface $builder The form builder
     * @param array                $options The options
     *
     * @see FormTypeExtensionInterface::buildForm()
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder->add('media', MediaType::class, [
            'label' => 'mediapagepart.image.choosefile',
            'mediatype' => 'image',
            'required' => true,
        ]);
        $builder->add('caption', TextType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.choosefile',
        ]);
        $builder->add('altText', TextType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.alttext',
        ]);
        $builder->add('link', URLChooserType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.link',
        ]);
        $builder->add('openInNewWindow', CheckboxType::class, [
            'required' => false,
            'label' => 'mediapagepart.image.openinnewwindow',
            'label_attr' => [
                'style' => 'margin-left: 5px',
            ],
        ]);
    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getBlockPrefix()
    {
        return 'imagepageparttype';
    }

    /**
     * Sets the default options for this type.
     *
     * @param OptionsResolver $resolver the resolver for the options
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'data_class' => ImagePagePart::class,
        ]);
    }
}
