<?php

namespace Hgabka\PagePartBundle\PagePartAdmin;

interface PagePartAdminConfiguratorInterface
{
    /**
     * @return array
     */
    public function getPossiblePagePartTypes();

    /**
     * @return string
     */
    public function getName();

    /**
     * @return string
     */
    public function getContext();

    /**
     * @return string
     */
    public function getConfig();

    /**
     * @return string
     */
    public function getWidgetTemplate();
}
