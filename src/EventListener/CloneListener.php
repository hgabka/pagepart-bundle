<?php

namespace Hgabka\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Hgabka\PagePartBundle\Entity\PagePartRef;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\Helper\HasPageTemplateInterface;
use Hgabka\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReaderInterface;
use Hgabka\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Hgabka\UtilsBundle\Event\DeepCloneAndSaveEvent;

/**
 * This event will make sure pageparts are being copied when deepClone is done on an entity implementing hasPagePartsInterface.
 */
class CloneListener
{
    /**
     * @var EntityManager|EntityManagerInterface
     */
    private $em;

    /**
     * @var PagePartConfigurationReaderInterface
     */
    private $pagePartReader;

    /**
     * @var PageTemplateConfigurationService
     */
    private $pageTemplateConfiguratiorService;

    public function __construct(
        EntityManagerInterface $em,
        PagePartConfigurationReaderInterface $pagePartReader,
        PageTemplateConfigurationService $pageTemplateConfiguratiorService
    ) {
        $this->em = $em;
        $this->pagePartReader = $pagePartReader;
        $this->pageTemplateConfiguratiorService = $pageTemplateConfiguratiorService;
    }

    /**
     * @param DeepCloneAndSaveEvent $event
     */
    public function postDeepCloneAndSave(DeepCloneAndSaveEvent $event)
    {
        $originalEntity = $event->getEntity();

        if ($originalEntity instanceof HasPagePartsInterface) {
            $clonedEntity = $event->getClonedEntity();

            $contexts = $this->pagePartReader->getPagePartContexts($originalEntity);
            foreach ($contexts as $context) {
                $this->em->getRepository(PagePartRef::class)->copyPageParts($this->em, $originalEntity, $clonedEntity, $context);
            }
        }

        if ($originalEntity instanceof HasPageTemplateInterface) {
            $clonedEntity = $event->getClonedEntity();
            $newPageTemplateConfiguration = clone $this->pageTemplateConfiguratiorService->findOrCreateFor($originalEntity);
            $newPageTemplateConfiguration->setId(null);
            $newPageTemplateConfiguration->setPageId($clonedEntity->getId());
            $this->em->persist($newPageTemplateConfiguration);
        }
    }
}
