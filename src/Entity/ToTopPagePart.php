<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\ToTopPagePartAdminType;

#[ORM\Entity]
#[ORM\Table(name: 'hg_page_part_to_top_page_parts')]
class ToTopPagePart extends AbstractPagePart
{
    public function __toString(): string
    {
        return 'ToTopPagePart';
    }

    public function getDefaultView(): string
    {
        return '@HgabkaPagePart/ToTopPagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return ToTopPagePartAdminType::class;
    }
}
