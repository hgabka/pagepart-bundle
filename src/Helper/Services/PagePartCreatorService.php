<?php

namespace Hgabka\PagePartBundle\Helper\Services;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Hgabka\NodeBundle\Entity\Node;
use Hgabka\NodeBundle\Entity\NodeTranslation;
use Hgabka\NodeBundle\Repository\NodeRepository;
use Hgabka\NodeBundle\Repository\NodeTranslationRepository;
use Hgabka\PagePartBundle\Entity\PagePartRef;
use Hgabka\PagePartBundle\Entity\PageTemplateConfiguration;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\Helper\HasPageTemplateInterface;
use Hgabka\PagePartBundle\Helper\PagePartInterface;
use Hgabka\PagePartBundle\Repository\PagePartRefRepository;
use Hgabka\PagePartBundle\Repository\PageTemplateConfigurationRepository;
use Hgabka\UtilsBundle\Entity\EntityInterface;
use Hgabka\UtilsBundle\Helper\ClassLookup;

/**
 * A class to facilitate the adding of PageParts to existing pages.
 *
 * NOTE: There is a similar implementation for adding pages. See the NodeBundle for more on this.
 */
class PagePartCreatorService
{
    /**
     * @var EntityManager|EntityManagerInterface
     */
    protected $em;

    /**
     * @var PagePartRefRepository
     */
    protected $pagePartRepo;

    /**
     * @var NodeTranslationRepository
     */
    protected $translationRepo;

    /**
     * @var NodeRepository
     */
    protected $nodeRepo;

    /**
     * Sets the EntityManager dependency.
     *
     * @param EntityManagerInterface $em
     */
    public function setEntityManager(EntityManagerInterface $em)
    {
        $this->em = $em;
        // Because these repositories are shared between the different functions it's
        // easier to make them available in the class.
        $this->pagePartRepo = $em->getRepository(PagePartRef::class);
        $this->translationRepo = $em->getRepository(NodeTranslation::class);
        $this->nodeRepo = $em->getRepository(Node::class);
    }

    /**
     * @return EntityManagerInterface
     */
    public function getEntityManager()
    {
        return $this->em;
    }

    /**
     * Add a single pagepart to an existing page for a specific language, in an optional position.
     *
     * @param mixed(Node|string)  $nodeOrInternalName
     *                                                A Node instance or the internal name.
     *                                                When the internal name is passed we'll get the node instance.
     *                                                Based on the language we'll locate the correct Page instance.
     * @param pagePartInterface $pagePart
     *                                    A completely configured pagepart for this language
     * @param string            $language
     *                                    The languagecode. nl|fr|en|.. . Just one.
     * @param string            $context
     *                                    Where you want the pagepart to be
     * @param mixed(integer\NULL) $position
     *                                                Leave null if you want to append at the end.
     *                                                Otherwise set a position you would like and it'll inject the pagepart in that position.
     *                                                It won't override pageparts but it will rather inject itself in that position and
     *                                                push the other pageparts down.
     */
    public function addPagePartToPage($nodeOrInternalName, PagePartInterface $pagePart, $language, $context = 'main', $position = null)
    {
        // Find the correct page instance.
        $node = $this->getNode($nodeOrInternalName);
        /** @var $translation NodeTranslation */
        $translation = $node->getNodeTranslation($language, true);
        /** @var HasPagePartsInterface $page */
        $page = $translation->getRef($this->em);

        // Find latest position.
        if (null === $position) {
            $pageParts = $this->pagePartRepo->getPagePartRefs($page, $context);
            $position = \count($pageParts) + 1;
        }

        $this->em->persist($pagePart);
        $this->em->flush();

        $this->pagePartRepo->addPagePart($page, $pagePart, $position, $context);
    }

    /**
     * A helper function to more easily append multiple pageparts in different manners.
     *
     * @param mixed(Node|string) $nodeOrInternalName
     *                                               The node that you'd like to append the pageparts to. It's also possible to provide an internalname.
     * @param array $structure
     *                         The structure array is something like this:
     *
     *      array('main' => array(
     *          function() { return new DummyPagePart('A') }, function() { return new DummyPagePart('B') }
     *       ), 'banners' => array($awesomeBanner));
     *
     *      So it's an array containing the pageparts per region. Each pagepart is returned by a function.
     *      This is clean because we don't have to bother with variablenames which we have to remember to pass
     *      to the pagecreatorservice at the right time. With this method it's impossible to assign a wrong pagepart to a page.
     *      Unless you provide the incorrect page oviously ... .
     *
     *      You can also include variables in the pagepart arrays if you want.
     *
     *      Or optionally you can use the results of the getCreatorArgumentsForPagePartAndProperties function instead of an anonymous function.
     * @param string $language
     *                         The language of the translation you want to append to
     *
     * @throws \LogicException
     */
    public function addPagePartsToPage($nodeOrInternalName, array $structure, $language)
    {
        $node = $this->getNode($nodeOrInternalName);

        // First instantiate all PageParts. This way no PageParts will be saved if there is an issue instantiating some of them.
        $instantiatedPageParts = [];
        foreach ($structure as $context => $pageParts) {
            $instantiatedPageParts[$context] = [];

            foreach ($pageParts as $pagePartOrFunction) {
                if (\is_callable($pagePartOrFunction)) {
                    $pagePartOrFunction = $pagePartOrFunction();

                    if (!isset($pagePartOrFunction) || (null === $pagePartOrFunction)) {
                        throw new \LogicException('A function returned nothing for a pagepart. Make sure you return your instantiated pageparts in your anonymous functions.');
                    }
                }
                if (!$pagePartOrFunction instanceof PagePartInterface) {
                    throw new \LogicException('Detected a supposed pagepart that did not implement the PagePartInterface.');
                }

                $instantiatedPageParts[$context][] = $pagePartOrFunction;
            }
        }

        // All good. We can start saving.
        foreach ($instantiatedPageParts as $context => $pageParts) {
            foreach ($pageParts as $pagePart) {
                $this->addPagePartToPage($node, $pagePart, $language, $context);
            }
        }
    }

    /**
     * @param mixed(Node|string) $nodeOrInternalName
     * @param string $language
     * @param string $templateName
     */
    public function setPageTemplate($nodeOrInternalName, $language, $templateName)
    {
        $node = $this->getNode($nodeOrInternalName);
        /** @var $translation NodeTranslation */
        $translation = $node->getNodeTranslation($language, true);
        /** @var EntityInterface|HasPageTemplateInterface $page */
        $page = $translation->getRef($this->em);

        /** @var PageTemplateConfigurationRepository $repo */
        $repo = $this->em->getRepository(PageTemplateConfiguration::class);
        $pageTemplateConfiguration = $repo->findFor($page);
        if ($pageTemplateConfiguration) {
            $pageTemplateConfiguration->setPageTemplate($templateName);
        } else {
            $pageTemplateConfiguration = new PageTemplateConfiguration();
            $pageTemplateConfiguration->setPageId($page->getId());
            $pageTemplateConfiguration->setPageEntityName(ClassLookup::getClass($page));
            $pageTemplateConfiguration->setPageTemplate($templateName);
        }

        $this->em->persist($pageTemplateConfiguration);
        $this->em->flush();
    }

    /**
     * A helper function to express what pagepart you want.
     *
     * It just accepts a classname and an array of setter functions with their requested values.
     *
     * It'll return an anonymous function which instantiates the pagepart.
     *
     * @param string $pagePartClassName the full class name of the pagepart you want to instantiate
     * @param array  $setters           An array of setternames and their values. array('setName' => 'Kim', 'isDeveloper' => true)
     *
     * @return callable the function that will instantiate a pagepart
     */
    public function getCreatorArgumentsForPagePartAndProperties($pagePartClassName, ?array $setters = null)
    {
        return function () use ($pagePartClassName, $setters) {
            $pp = new $pagePartClassName();

            if (null !== $setters) {
                foreach ($setters as $setter => $value) {
                    \call_user_func([$pp, $setter], $value);
                }
            }

            return $pp;
        };
    }

    /**
     * @param mixed(string|Node) $nodeOrInternalName
     *
     * @return object
     */
    private function getNode($nodeOrInternalName)
    {
        if (\is_string($nodeOrInternalName)) {
            return $this->nodeRepo->findOneBy(['internalName' => $nodeOrInternalName]);
        }

        return $nodeOrInternalName;
    }
}
