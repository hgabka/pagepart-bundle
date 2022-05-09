<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\TocPagePartAdminType;

#[ORM\Entity]
#[ORM\Table(name: 'hg_page_part_toc_page_parts')]
class TocPagePart extends AbstractPagePart
{
    public function __toString(): string
    {
        return 'TocPagePart';
    }

    public function getDefaultView(): string
    {
        return '@HgabkaPagePart/TocPagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return TocPagePartAdminType::class;
    }
}
