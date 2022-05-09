<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\Helper\PagePartInterface;

/**
 * Abstract ORM Pagepart.
 */
abstract class AbstractPagePart implements PagePartInterface
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AbstractPagePart
     */
    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * In most cases, the backend view will not differ from the default one.
     * Also, this implementation guarantees backwards compatibility.
     *
     * @return string
     */
    public function getAdminView(): ?string
    {
        return $this->getDefaultView();
    }

    /**
     * Use this method to override the default view for a specific page type.
     * Also, this implementation guarantees backwards compatibility.
     *
     * @return string
     */
    public function getView(?HasPagePartsInterface $page = null): ?string
    {
        return $this->getDefaultView();
    }
}
