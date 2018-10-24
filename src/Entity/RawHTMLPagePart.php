<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\RawHTMLPagePartAdminType;

/**
 * Class that defines a raw html page part object to add to a page.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_page_part_raw_html_page_parts")
 */
class RawHTMLPagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $content;

    /**
     * @return string
     */
    public function __toString()
    {
        return 'RawHTMLPagePart '.htmlentities($this->getContent());
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param string $content
     *
     * @return RawHTMLPagePart
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return 'HgabkaPagePartBundle:RawHTMLPagePart:view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return RawHTMLPagePartAdminType::class;
    }
}
