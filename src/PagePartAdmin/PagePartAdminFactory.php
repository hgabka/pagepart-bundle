<?php

namespace Hgabka\PagePartBundle\PagePartAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * PagePartAdminFactory.
 */
class PagePartAdminFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param PagePartAdminConfiguratorInterface $configurator The configurator
     * @param EntityManagerInterface             $em           The entity manager
     * @param HasPagePartsInterface              $page         The page
     * @param null|string                        $context      The context
     *
     * @return PagePartAdmin
     */
    public function createList(PagePartAdminConfiguratorInterface $configurator, EntityManagerInterface $em, HasPagePartsInterface $page, $context = null)
    {
        return new PagePartAdmin($configurator, $em, $page, $context, $this->container);
    }
}
