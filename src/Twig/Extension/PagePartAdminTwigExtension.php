<?php

namespace Hgabka\PagePartBundle\Twig\Extension;

use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Twig\Extension\AbstractExtension;
use Twig\Environment;
use Twig\TwigFunction;

/**
 * PagePartAdminTwigExtension.
 */
class PagePartAdminTwigExtension extends AbstractExtension
{
    private $usesExtendedPagePartChooser = false;

    /**
     * @return array
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('pagepartadmin_widget', [$this, 'renderWidget'], ['needs_environment' => true, 'is_safe' => ['html']]),
        ];
    }

    /**
     * Renders the HTML for a given pagepart.
     *
     * Example usage in Twig:
     *
     *     {{ pagepartadmin_widget(ppAdmin) }}
     *
     * You can pass options during the call:
     *
     *     {{ pagepartadmin_widget(ppAdmin, {'attr': {'class': 'foo'}}) }}
     *
     *     {{ pagepartadmin_widget(ppAdmin, {'separator': '+++++'}) }}
     *
     * @param \Twig_Environment $env
     * @param PagePartAdmin     $ppAdmin      The pagepart admin to render
     * @param Form              $form         The form
     * @param array             $parameters   Additional variables passed to the template
     * @param string            $templateName
     *
     * @return string The html markup
     */
    public function renderWidget(Environment $env, PagePartAdmin $ppAdmin, $form = null, array $parameters = [], $templateName = null)
    {
        if (null === $templateName) {
            $templateName = '@HgabkaPagePart/PagePartAdminTwigExtension/widget.html.twig';
        }

        $template = $env->load($templateName);

        return $template->render(array_merge($parameters, [
            'pagepartadmin' => $ppAdmin,
            'page' => $ppAdmin->getPage(),
            'form' => $form,
            'extended' => $this->usesExtendedPagePartChooser,
            'config' => $ppAdmin->getConfig(),
        ]));
    }

    /**
     * Get usesExtendedPagePartChooser.
     *
     * @return usesExtendedPagePartChooser
     */
    public function getUsesExtendedPagePartChooser()
    {
        return $this->usesExtendedPagePartChooser;
    }

    /**
     * Set usesExtendedPagePartChooser.
     *
     * @param usesExtendedPagePartChooser the value to set
     * @param mixed $usesExtendedPagePartChooser
     */
    public function setUsesExtendedPagePartChooser($usesExtendedPagePartChooser)
    {
        $this->usesExtendedPagePartChooser = $usesExtendedPagePartChooser;
    }
}
