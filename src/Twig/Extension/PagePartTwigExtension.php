<?php

namespace Hgabka\PagePartBundle\Twig\Extension;

use Doctrine\ORM\EntityManager;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\Helper\PagePartInterface;
use Hgabka\PagePartBundle\Repository\PagePartRefRepository;

/**
 * PagePartTwigExtension.
 */
class PagePartTwigExtension extends \Twig_Extension
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('render_pageparts', [$this, 'renderPageParts'], ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['html']]),
            new \Twig_SimpleFunction('getpageparts', ['needs_environment' => true, $this, 'getPageParts']),
        ];
    }

    /**
     * @param \Twig_Environment     $env
     * @param array                 $twigContext The twig context
     * @param HasPagePartsInterface $page        The page
     * @param string                $contextName The pagepart context
     * @param array                 $parameters  Some extra parameters
     *
     * @return string
     */
    public function renderPageParts(\Twig_Environment $env, array $twigContext, HasPagePartsInterface $page, $contextName = 'main', array $parameters = [])
    {
        $template = $env->loadTemplate('HgabkaPagePartBundle:PagePartTwigExtension:widget.html.twig');
        // @var $entityRepository PagePartRefRepository
        $pageparts = $this->getPageParts($page, $contextName);
        $newTwigContext = array_merge($parameters, [
            'pageparts' => $pageparts,
            'page' => $page,
        ]);
        $newTwigContext = array_merge($newTwigContext, $twigContext);

        return $template->render($newTwigContext);
    }

    /**
     * @param HasPagePartsInterface $page    The page
     * @param string                $context The pagepart context
     *
     * @return PagePartInterface[]
     */
    public function getPageParts(HasPagePartsInterface $page, $context = 'main')
    {
        // @var $entityRepository PagePartRefRepository
        $entityRepository = $this->em->getRepository('KunstmaanPagePartBundle:PagePartRef');
        $pageparts = $entityRepository->getPageParts($page, $context);

        return $pageparts;
    }
}
