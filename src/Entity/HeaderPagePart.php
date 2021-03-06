<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\HeaderPagePartAdminType;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;

/**
 * Class that defines a header page part object to add to a page.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_page_part_header_page_parts")
 */
class HeaderPagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $niv;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $title;

    /**
     * @return string
     */
    public function __toString()
    {
        return 'HeaderPagePart '.$this->getTitle();
    }

    /**
     * @param ClassMetadata $metadata
     */
    public static function loadValidatorMetadata(ClassMetadata $metadata)
    {
        $metadata->addPropertyConstraint('niv', new NotBlank(['message' => 'headerpagepart.niv.not_blank']));
        $metadata->addPropertyConstraint('title', new NotBlank(['message' => 'headerpagepart.title.not_blank']));
    }

    /**
     * Set niv.
     *
     * @param int $niv
     *
     * @return HeaderPagePart
     */
    public function setNiv($niv)
    {
        $this->niv = $niv;

        return $this;
    }

    /**
     * Get niv.
     *
     * @return int
     */
    public function getNiv()
    {
        return $this->niv;
    }

    /**
     * @param string $title
     *
     * @return HeaderPagePart
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '@HgabkaPagePart/HeaderPagePart/view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return HeaderPagePartAdminType::class;
    }
}
