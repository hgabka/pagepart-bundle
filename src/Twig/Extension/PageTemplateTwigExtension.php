<?php

namespace Hgabka\PagePartBundle\Twig\Extension;

use Hgabka\PagePartBundle\Helper\HasPageTemplateInterface;
use Hgabka\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * PagePartTwigExtension.
 */
class PageTemplateTwigExtension extends AbstractExtension
{
    /**
     * @var PageTemplateConfigurationService
     */
    private $templateConfiguration;

    public function __construct(PageTemplateConfigurationService $templateConfiguration)
    {
        $this->templateConfiguration = $templateConfiguration;
    }

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('render_pagetemplate', [$this, 'renderPageTemplate'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
            new TwigFunction('getpagetemplate', [$this, 'getPageTemplate']),
            new TwigFunction('render_pagetemplate_configuration', [$this, 'renderPageTemplateConfiguration'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * @param \Twig_Environment        $env
     * @param array                    $twigContext
     * @param HasPageTemplateInterface $page
     * @param array                    $parameters
     *
     * @return string
     */
    public function renderPageTemplate(Environment $env, array $twigContext, HasPageTemplateInterface $page, array $parameters = [])
    {
        $pageTemplates = $this->templateConfiguration->getPageTemplates($page);

        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $env->load($pageTemplate->getTemplate());

        return $template->render(array_merge($parameters, $twigContext));
    }

    /**
     * @param HasPageTemplateInterface $page The page
     *
     * @return string
     */
    public function getPageTemplate(HasPageTemplateInterface $page)
    {
        return $this->templateConfiguration->findOrCreateFor($page)->getPageTemplate();
    }

    /**
     * @param \Twig_Environment        $env
     * @param array                    $twigContext
     * @param HasPageTemplateInterface $page
     * @param array                    $parameters
     *
     * @return string
     */
    public function renderPageTemplateConfiguration(\Twig_Environment $env, array $twigContext, HasPageTemplateInterface $page, array $parameters = [])
    {
        $pageTemplates = $this->templateConfiguration->getPageTemplates($page);

        $pageTemplate = $pageTemplates[$this->getPageTemplate($page)];

        $template = $env->loadTemplate($parameters['template']);

        return $template->render(array_merge(['pageTemplate' => $pageTemplate], $twigContext));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'pagetemplate_twig_extension';
    }
}
