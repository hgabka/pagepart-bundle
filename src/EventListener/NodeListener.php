<?php

namespace Hgabka\PagePartBundle\EventListener;

use Doctrine\ORM\EntityManagerInterface;
use Hgabka\NodeBundle\Event\AdaptFormEvent;
use Hgabka\PagePartBundle\Helper\FormWidgets\PagePartWidget;
use Hgabka\PagePartBundle\Helper\FormWidgets\PageTemplateWidget;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\Helper\HasPageTemplateInterface;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Hgabka\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReaderInterface;
use Hgabka\PagePartBundle\PageTemplate\PageTemplateConfigurationReaderInterface;
use Hgabka\PagePartBundle\PageTemplate\PageTemplateConfigurationService;
use Hgabka\UtilsBundle\Helper\FormWidgets\ListWidget;
use Hgabka\UtilsBundle\Helper\FormWidgets\Tabs\Tab;

/**
 * NodeListener.
 */
class NodeListener
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var PagePartAdminFactory
     */
    private $pagePartAdminFactory;

    /**
     * @var PageTemplateConfigurationReaderInterface
     */
    private $templateReader;

    /**
     * @var PagePartConfigurationReaderInterface
     */
    private $pagePartReader;

    /**
     * @var PageTemplateConfigurationService
     */
    private $pageTemplateConfiguratiorService;

    public function __construct(
        EntityManagerInterface $em,
        PagePartAdminFactory $pagePartAdminFactory,
        PageTemplateConfigurationReaderInterface $templateReader,
        PagePartConfigurationReaderInterface $pagePartReader,
        PageTemplateConfigurationService $pageTemplateConfiguratiorService
    ) {
        $this->em = $em;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
        $this->templateReader = $templateReader;
        $this->pagePartReader = $pagePartReader;
        $this->pageTemplateConfiguratiorService = $pageTemplateConfiguratiorService;
    }

    /**
     * @param AdaptFormEvent $event
     */
    public function adaptForm(AdaptFormEvent $event)
    {
        $page = $event->getPage();
        $tabPane = $event->getTabPane();

        if ($page instanceof HasPageTemplateInterface) {
            $pageTemplateWidget = new PageTemplateWidget($page, $event->getRequest(), $this->em, $this->pagePartAdminFactory, $this->templateReader, $this->pagePartReader, $this->pageTemplateConfiguratiorService);

            // @var Tab $propertiesTab
            $propertiesTab = $tabPane->getTabByTitle('hg_node.tab.properties.title');
            if (null !== $propertiesTab) {
                $propertiesWidget = $propertiesTab->getWidget();
                $tabPane->removeTab($propertiesTab);
                $tabPane->addTab(new Tab('hg_pagepart.tab.content.title', new ListWidget([$propertiesWidget, $pageTemplateWidget])), 0);
            } else {
                $tabPane->addTab(new Tab('hg_pagepart.tab.content.title', $pageTemplateWidget), 0);
            }
        } elseif ($page instanceof HasPagePartsInterface) {
            // @var HasPagePartsInterface $page
            $pagePartAdminConfigurators = $this->pagePartReader->getPagePartAdminConfigurators($page);

            foreach ($pagePartAdminConfigurators as $index => $pagePartAdminConfiguration) {
                $pagePartWidget = new PagePartWidget($page, $event->getRequest(), $this->em, $pagePartAdminConfiguration, $this->pagePartAdminFactory);
                if (0 === $index) {
                    // @var Tab $propertiesTab
                    $propertiesTab = $tabPane->getTabByTitle('hg_node.tab.properties.title');

                    if (null !== $propertiesTab) {
                        $propertiesWidget = $propertiesTab->getWidget();
                        $tabPane->removeTab($propertiesTab);
                        $tabPane->addTab(new Tab($pagePartAdminConfiguration->getName(), new ListWidget([$propertiesWidget, $pagePartWidget])), 0);

                        continue;
                    }
                }
                $tabPane->addTab(new Tab($pagePartAdminConfiguration->getName(), $pagePartWidget), \count($tabPane->getTabs()));
            }
        }
    }
}
