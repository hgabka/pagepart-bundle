<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\LinkPagePartAdminType;

#[ORM\Entity]
#[ORM\Table(name: 'hg_page_part_link_page_parts')]
class LinkPagePart extends AbstractPagePart
{
    #[ORM\Column(name: 'url', type: 'string', nullable: true)]
    protected ?string $url = null;

    #[ORM\Column(name: 'openinnewwindow', type: 'boolean', nullable: true)]
    protected ?bool $openinnewwindow = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    #[ORM\Column(name: '`text`', type: 'string', nullable: true)]
    protected ?string $text = null;

    public function __toString(): string
    {
        return 'LinkPagePart';
    }

    public function setUrl(?string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function getOpenInNewWindow(): ?bool
    {
        return $this->openinnewwindow;
    }

    public function setOpenInNewWindow(?bool $openInNewWindow): self
    {
        $this->openinnewwindow = $openInNewWindow;

        return $this;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getDefaultView(): string
    {
        return '@HgabkaPagePart/LinkPagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return LinkPagePartAdminType::class;
    }
}
