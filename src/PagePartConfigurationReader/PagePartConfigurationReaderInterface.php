<?php

namespace Hgabka\PagePartBundle\PagePartConfigurationReader;

use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\PagePartAdmin\AbstractPagePartAdminConfigurator;

interface PagePartConfigurationReaderInterface
{
    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     *
     * @return AbstractPagePartAdminConfigurator[]
     */
    public function getPagePartAdminConfigurators(HasPagePartsInterface $page);

    /**
     * @param HasPagePartsInterface $page
     *
     * @throws \Exception
     *
     * @return string[]
     */
    public function getPagePartContexts(HasPagePartsInterface $page);
}
