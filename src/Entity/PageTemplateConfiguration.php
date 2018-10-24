<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\NodeBundle\Entity\PageInterface;

/**
 * Configuration for page templates.
 *
 * @ORM\Entity(repositoryClass="Hgabka\PagePartBundle\Repository\PageTemplateConfigurationRepository")
 * @ORM\Table(name="hg_page_part_page_template_configuration", indexes={@ORM\Index(name="idx_page_template_config_search", columns={"page_id", "page_entity_name"})})
 */
class PageTemplateConfiguration
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint", name="page_id")
     */
    protected $pageId;

    /**
     * @ORM\Column(type="string", name="page_entity_name")
     */
    protected $pageEntityName;

    /**
     * @ORM\Column(type="string", name="page_template")
     */
    protected $pageTemplate;

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
     * Get pageId.
     *
     * @return int
     */
    public function getPageId()
    {
        return $this->pageId;
    }

    /**
     * @param int $id
     *
     * @return PageTemplateConfiguration
     */
    public function setPageId($id)
    {
        $this->pageId = $id;

        return $this;
    }

    /**
     * Get pageEntityname.
     *
     * @return string
     */
    public function getPageEntityName()
    {
        return $this->pageEntityName;
    }

    /**
     * Set pageEntityname.
     *
     * @param string $pageEntityName
     *
     * @return PageTemplateConfiguration
     */
    public function setPageEntityName($pageEntityName)
    {
        $this->pageEntityName = $pageEntityName;

        return $this;
    }

    /**
     * get pageTemplate.
     *
     * @return string
     */
    public function getPageTemplate()
    {
        return $this->pageTemplate;
    }

    /**
     * Set pagetemplate.
     *
     * @param string $pageTemplate
     *
     * @return PageTemplateConfiguration
     */
    public function setPageTemplate($pageTemplate)
    {
        $this->pageTemplate = $pageTemplate;

        return $this;
    }

    /**
     * @param \Doctrine\ORM\EntityManager $em
     *
     * @return PageInterface
     */
    public function getPage(EntityManager $em)
    {
        return $em->getRepository($this->getPageEntityname())->find($this->getPageId());
    }
}
