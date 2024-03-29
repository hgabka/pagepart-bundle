<?php

namespace Hgabka\PagePartBundle\Event;

use Hgabka\PagePartBundle\Helper\PagePartInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\Event;

/**
 * PagePartEvent.
 */
class PagePartEvent extends Event
{
    /**
     * @var PagePartInterface
     */
    protected $pagePart;

    /**
     * @var Response
     */
    private $response;

    /**
     * PagePartEvent constructor.
     *
     * @param PagePartInterface $pagePart
     */
    public function __construct(PagePartInterface $pagePart)
    {
        $this->pagePart = $pagePart;
    }

    /**
     * @return PagePartInterface
     */
    public function getPagePart()
    {
        return $this->pagePart;
    }

    /**
     * @param PagePartInterface $pagePart
     */
    public function setPagePart(PagePartInterface $pagePart)
    {
        $this->pagePart = $pagePart;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param Response $response
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
    }
}
