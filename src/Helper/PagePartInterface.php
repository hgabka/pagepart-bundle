<?php

namespace Hgabka\PagePartBundle\Helper;

use Hgabka\UtilsBundle\Entity\EntityInterface;

/**
 * PagePartInterface.
 */
interface PagePartInterface extends EntityInterface
{
    /**
     * Returns the view used in the frontend.
     *
     * @return string
     */
    public function getDefaultView(): ?string;

    /**
     * Returns the view used in the backend.
     *
     * @return string
     */
    public function getAdminView(): ?string;

    /**
     * This method can be used to override the default view for a specific page type.
     *
     * @param null|HasPagePartsInterface $page
     *
     * @return string
     */
    public function getView(?HasPagePartsInterface $page = null): ?string;

    /**
     * Returns the default backend form type for the page part.
     *
     * @return string fully qualified class name of a form
     */
    public function getDefaultAdminType(): ?string;
}
