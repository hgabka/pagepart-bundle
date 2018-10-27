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
    protected $id;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return AbstractPagePart
     */
    public function setId($id)
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
    public function getAdminView()
    {
        return $this->getDefaultView();
    }

    /**
     * Use this method to override the default view for a specific page type.
     * Also, this implementation guarantees backwards compatibility.
     *
     * @return string
     */
    public function getView(HasPagePartsInterface $page = null)
    {
        return $this->getDefaultView();
    }
}
