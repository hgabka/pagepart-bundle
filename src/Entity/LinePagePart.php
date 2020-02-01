<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\LinePagePartAdminType;

/**
 * LinePagePart.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_page_part_line_page_parts")
 */
class LinePagePart extends AbstractPagePart
{
    /**
     * {@inheritdoc}
     */
    public function __toString()
    {
        return 'LinePagePart';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultView()
    {
        return '@HgabkaPagePart/LinePagePart/view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return LinePagePartAdminType::class;
    }
}
