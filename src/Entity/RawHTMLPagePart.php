<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\RawHTMLPagePartAdminType;

#[ORM\Entity]
#[ORM\Table(name: 'hg_page_part_raw_html_page_parts')]
class RawHTMLPagePart extends AbstractPagePart
{
    #[ORM\Column(name: 'content', type: 'text', nullable: true)]
    protected ?string $content = null;

    public function __toString(): string
    {
        return 'RawHTMLPagePart ' . htmlentities($this->getContent());
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(?string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getDefaultView(): string
    {
        return '@HgabkaPagePart/RawHTMLPagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return RawHTMLPagePartAdminType::class;
    }
}
