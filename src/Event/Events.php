<?php

namespace Hgabka\PagePartBundle\Event;

/**
 * Events.
 */
class Events
{
    /**
     * The postPersist event occurs for a given pagePart, after the pagePart is persisted.
     *
     * @var string
     */
    const POST_PERSIST = 'hgabka_pagepart.postPersist';
}
