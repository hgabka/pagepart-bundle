<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\TocPagePartAdminType;

/**
 * TocPagePart.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_page_part_toc_page_parts")
 */
class TocPagePart extends AbstractPagePart
{
    /**
     * @return string
     */
    public function __toString()
    {
        return 'TocPagePart';
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return 'HgabkaPagePartBundle:TocPagePart:view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return TocPagePartAdminType::class;
    }
}
