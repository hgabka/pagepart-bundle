<?php

namespace Hgabka\PagePartBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Hgabka\PagePartBundle\Form\LinkPagePartAdminType;

/**
 * LinkPagePart.
 *
 * @ORM\Entity
 * @ORM\Table(name="hg_page_part_link_page_parts")
 */
class LinkPagePart extends AbstractPagePart
{
    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $url;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    protected $openinnewwindow;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $text;

    /**
     * @return string
     */
    public function __toString()
    {
        return 'LinkPagePart';
    }

    /**
     * @param string $url
     *
     * @return LinkPagePart
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return bool
     */
    public function getOpenInNewWindow()
    {
        return $this->openinnewwindow;
    }

    /**
     * @param bool $openInNewWindow
     *
     * @return LinkPagePart
     */
    public function setOpenInNewWindow($openInNewWindow)
    {
        $this->openinnewwindow = $openInNewWindow;

        return $this;
    }

    /**
     * @param string $text
     *
     * @return LinkPagePart
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * @return string
     */
    public function getText()
    {
        return $this->text;
    }

    /**
     * @return string
     */
    public function getDefaultView()
    {
        return '@HgabkaPagePart/LinkPagePart/view.html.twig';
    }

    /**
     * {@inheritdoc}
     */
    public function getDefaultAdminType()
    {
        return LinkPagePartAdminType::class;
    }
}
