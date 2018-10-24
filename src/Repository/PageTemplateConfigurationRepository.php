<?php

namespace Hgabka\PagePartBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Hgabka\PagePartBundle\Entity\PageTemplateConfiguration;
use Hgabka\PagePartBundle\Helper\HasPageTemplateInterface;
use Hgabka\UtilsBundle\Helper\ClassLookup;

/**
 * PageTemplateConfigurationRepository.
 */
class PageTemplateConfigurationRepository extends EntityRepository
{
    /**
     * @param HasPageTemplateInterface $page
     *
     * @return PageTemplateConfiguration
     */
    public function findFor(HasPageTemplateInterface $page)
    {
        return $this->findOneBy(['pageId' => $page->getId(), 'pageEntityName' => ClassLookup::getClass($page)]);
    }
}
