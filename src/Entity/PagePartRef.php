<?php

namespace Hgabka\PagePartBundle\Entity;

use DateTime;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Helper\PagePartInterface;
use Hgabka\PagePartBundle\Repository\PagePartRefRepository;

#[ORM\Entity(repositoryClass: PagePartRefRepository::class)]
#[ORM\Table(name: 'hg_page_part_page_part_refs')]
#[ORM\Index(name: 'idx_page_part_search', columns: ['page_id', 'page_entityname', 'context'])]
#[ORM\HasLifecycleCallbacks]
class PagePartRef
{
    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    protected ?int $id = null;

    #[ORM\Column(name: 'page_id', type: 'bigint')]
    protected ?int $pageId = null;

    #[ORM\Column(name: 'page_entityname', type: 'string')]
    protected ?string $pageEntityname = null;

    #[ORM\Column(name: 'context', type: 'string')]
    protected ?string $context = null;

    #[ORM\Column(name: 'sequencenumber', type: 'integer')]
    protected ?int $sequencenumber = null;

    #[ORM\Column(name: 'page_part_id', type: 'bigint')]
    protected ?int $pagePartId = null;

    #[ORM\Column(name: 'page_part_entityname', type: 'string')]
    protected ?string $pagePartEntityname = null;

    #[ORM\Column(name: 'created', type: 'datetime')]
    protected ?DateTime $created = null;

    #[ORM\Column(name: 'updated', type: 'datetime')]
    protected ?DateTime $updated = null;

    public function __construct()
    {
        $this->setCreated(new DateTime());
        $this->setUpdated(new DateTime());
    }

    public function __toString(): string
    {
        return 'pagepartref in context ' . $this->getContext();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $num): self
    {
        $this->id = $num;
        
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

    public function getPageEntityname(): ?string
    {
        return $this->pageEntityname;
    }

    public function setPageEntityname(?string $pageEntityname): self
    {
        $this->pageEntityname = $pageEntityname;
        
        return $this;
    }

    public function getContext(): ?string
    {
        return $this->context;
    }

    public function setContext(?string $context): self
    {
        $this->context = $context;
        
        return $this;
    }

    public function getSequencenumber(): ?int
    {
        return $this->sequencenumber;
    }

    public function setSequencenumber(?int $sequencenumber): self
    {
        $this->sequencenumber = $sequencenumber;
        
        return $this;
    }

    public function getPagePartId(): ?int
    {
        return $this->pagePartId;
    }

    public function setPagePartId(?int $pagePartId): self
    {
        $this->pagePartId = $pagePartId;
        
        return $this;
    }

    public function getPagePartEntityname(): ?string
    {
        return $this->pagePartEntityname;
    }

    public function setPagePartEntityname(?string $pagePartEntityname): self
    {
        $this->pagePartEntityname = $pagePartEntityname;
        
        return $this;
    }

    public function getCreated(): ?DateTime
    {
        return $this->created;
    }

    public function setCreated(?DateTime $created): self
    {
        $this->created = $created;
        
        return $this;
    }

    public function getUpdated(): ?DateTime
    {
        return $this->updated;
    }

    public function setUpdated(?DateTime $updated): self
    {
        $this->updated = $updated;
        
        return $this;
    }

    #[ORM\PreUpdate]
    public function setUpdatedValue(): self
    {
        $this->setUpdated(new DateTime());

        return $this;
    }

    public function getPagePart(EntityManager $em): ?PagePartInterface
    {
        return $em->getRepository($this->getPagePartEntityname())->find($this->getPagePartId());
    }
}
