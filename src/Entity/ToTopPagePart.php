<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\ToTopPagePartAdminType;

/**
 * ToTopPagePart.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_page_part_to_top_page_parts")
 */
class ToTopPagePart extends AbstractPagePart
{
    /**
     * @return string
     */
    public function __toString()
    {
        return 'ToTopPagePart';
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return 'HgabkaPagePartBundle:ToTopPagePart:view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return ToTopPagePartAdminType::class;
    }
}
