<?php

namespace Hgabka\PagePartBundle\Helper;

use Hgabka\PagePartBundle\PageTemplate\PageTemplateInterface;

/**
 * HasPageTemplateInterface.
 */
interface HasPageTemplateInterface extends HasPagePartsInterface
{
    /**
     * @return PageTemplateInterface[]
     */
    public function getPageTemplates();
}
