<?php

namespace Hgabka\PagePartBundle\PagePartAdmin;

use Hgabka\PagePartBundle\Entity\HeaderPagePart;
use Hgabka\PagePartBundle\Entity\LinePagePart;
use Hgabka\PagePartBundle\Entity\LinkPagePart;
use Hgabka\PagePartBundle\Entity\RawHTMLPagePart;
use Hgabka\PagePartBundle\Entity\TextPagePart;
use Hgabka\PagePartBundle\Entity\TocPagePart;
use Hgabka\PagePartBundle\Entity\ToTopPagePart;

/**
 * Builder.
 */
class Builder
{
    /**
     * @return array
     */
    public function getPageParts()
    {
        $pageParts = [['name' => 'Header', 'class' => HeaderPagePart::class],
                           ['name' => 'Text', 'class' => TextPagePart::class],
                           ['name' => 'Link', 'class' => LinkPagePart::class],
                           ['name' => 'Raw HTML', 'class' => RawHTMLPagePart::class],
                           ['name' => 'Line', 'class' => LinePagePart::class],
                           ['name' => 'TOC', 'class' => TocPagePart::class],
                           ['name' => 'Link To Top', 'class' => ToTopPagePart::class], ];

        return $pageParts;
    }
}
