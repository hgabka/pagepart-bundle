<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\NodeBundle\Entity\PageInterface;
use Hgabka\PagePartBundle\Repository\PageTemplateConfigurationRepository;

#[ORM\Entity(repositoryClass: PageTemplateConfigurationRepository::class)]
#[ORM\Table(name: 'hg_page_part_page_template_configuration')]
#[ORM\Index(name: 'idx_page_template_config_search', columns: ['page_id', 'page_entity_name'])]
class PageTemplateConfiguration
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'page_id', type: 'bigint')]
    protected ?int $pageId = null;

    #[ORM\Column(name: 'page_entity_name', type: 'string')]
    protected ?string $pageEntityName = null;

    #[ORM\Column(name: 'page_template', type: 'string')]
    protected ?string $pageTemplate = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getPageId(): ?int
    {
        return $this->pageId;
    }

    public function setPageId(?int $id): self
    {
        $this->pageId = $id;

        return $this;
    }

    public function getPageEntityName(): ?string
    {
        return $this->pageEntityName;
    }

    public function setPageEntityName(?string $pageEntityName): self
    {
        $this->pageEntityName = $pageEntityName;

        return $this;
    }

    public function getPageTemplate(): ?string
    {
        return $this->pageTemplate;
    }

    public function setPageTemplate(?string $pageTemplate): self
    {
        $this->pageTemplate = $pageTemplate;

        return $this;
    }

    public function getPage(EntityManager $em): ?PageInterface
    {
        return $em->getRepository($this->getPageEntityname())->find($this->getPageId());
    }
}
