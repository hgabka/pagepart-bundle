<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\HeaderPagePartAdminType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

#[ORM\Entity]
#[ORM\Table(name: 'hg_page_part_header_page_parts')]
class HeaderPagePart extends AbstractPagePart
{
    #[ORM\Column(name: 'niv', type: 'integer', nullable: true)]
    protected ?int $niv = null;

    #[ORM\Column(name: 'title', type: 'string', nullable: true)]
    protected ?string $title = null;

    public function __toString(): string
    {
        return 'HeaderPagePart ' . $this->getTitle();
    }

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('niv', new NotBlank(['message' => 'headerpagepart.niv.not_blank']));
        $metadata->addPropertyConstraint('title', new NotBlank(['message' => 'headerpagepart.title.not_blank']));
    }

    public function setNiv(?int $niv): self
    {
        $this->niv = $niv;

        return $this;
    }

    public function getNiv(): ?int
    {
        return $this->niv;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDefaultView(): string
    {
        return '@HgabkaPagePart/HeaderPagePart/view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType(): string
    {
        return HeaderPagePartAdminType::class;
    }
}
