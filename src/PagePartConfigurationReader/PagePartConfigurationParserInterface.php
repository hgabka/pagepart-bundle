<?php

namespace Hgabka\PagePartBundle\PagePartConfigurationReader;

use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;

interface PagePartConfigurationParserInterface
{
    /**
     * This will read the $name file and parse it to the PageTemplate.
     *
     * @param string                               $name
     * @param PagePartAdminConfiguratorInterface[] $existing
     *
     * @throws \Exception
     *
     * @return PagePartAdminConfiguratorInterface
     */
    public function parse($name, array $existing = []);
}
