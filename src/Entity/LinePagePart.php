<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\LinePagePartAdminType;

#[ORM\Entity]
#[ORM\Table(name: 'hg_page_part_line_page_parts')]
class LinePagePart extends AbstractPagePart
{
    public function __toString(): string
    {
        return 'LinePagePart';
    }

    public function getDefaultView(): string
    {
        return '@HgabkaPagePart/LinePagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return LinePagePartAdminType::class;
    }
}
