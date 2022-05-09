<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\MediaBundle\Entity\Media;
use Hgabka\PagePartBundle\Form\ImagePagePartAdminType;

#[ORM\Entity]
#[ORM\Table(name: 'hg_page_part_image_page_parts')]
class ImagePagePart extends AbstractPagePart
{
    #[ORM\ManyToOne(targetEntity: Media::class)]
    #[ORM\JoinColumn(name: 'media_id', referencedColumnName: 'id')]
    private ?Media $media = null;

    #[ORM\Column(name: 'caption', type: 'string', nullable: true)]
    private ?string $caption = null;

    #[ORM\Column(name: 'alt_text', type: 'string', nullable: true)]
    private ?string $altText = null;

    #[ORM\Column(name: 'link', type: 'string', nullable: true)]
    private ?string $link = null;

    #[ORM\Column(name: 'open_in_new_window', type: 'boolean', nullable: true)]
    private ?bool $openInNewWindow = null;

    public function getOpenInNewWindow(): ?bool
    {
        return $this->openInNewWindow;
    }

    public function setOpenInNewWindow(?bool $openInNewWindow): self
    {
        $this->openInNewWindow = $openInNewWindow;

        return $this;
    }

    public function setLink(?string $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getLink(): ?string
    {
        return $this->link;
    }

    public function setAltText(?string $altText): self
    {
        $this->altText = $altText;

        return $this;
    }

    public function getAltText(): ?string
    {
        return $this->altText;
    }

    public function getMedia(): ?Media
    {
        return $this->media;
    }

    public function setMedia(?Media $media): self
    {
        $this->media = $media;

        return $this;
    }

    public function setCaption(?string $caption): self
    {
        $this->caption = $caption;

        return $this;
    }

    public function getCaption(): ?string
    {
        return $this->caption;
    }

    public function getDefaultView(): string
    {
        return '@HgabkaPagePart/ImagePagePart/view.html.twig';
    }

    public function getDefaultAdminType(): string
    {
        return ImagePagePartAdminType::class;
    }
}
