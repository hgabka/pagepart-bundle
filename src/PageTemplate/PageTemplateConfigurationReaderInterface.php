<?php

namespace Hgabka\PagePartBundle\PageTemplate;

use Hgabka\PagePartBundle\Helper\HasPageTemplateInterface;

interface PageTemplateConfigurationReaderInterface
{
    /**
     * @param HasPageTemplateInterface $page
     *
     * @throws \Exception
     *
     * @return PageTemplateInterface[]
     */
    public function getPageTemplates(HasPageTemplateInterface $page);
}
