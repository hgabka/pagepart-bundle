<?php

namespace Hgabka\PagePartBundle\PagePartAdmin;

use Doctrine\ORM\EntityManagerInterface;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * PagePartAdminFactory.
 */
class PagePartAdminFactory
{
    /**
     * @var EventDispatcher
     */
    private $eventDispatcher;

    /**
     * Constructor.
     *
     * @param ContainerInterface $container
     */
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
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
        return new PagePartAdmin($configurator, $em, $page, $context, $this->eventDispatcher);
    }
}
