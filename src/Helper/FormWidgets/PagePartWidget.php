<?php

namespace Hgabka\PagePartBundle\Helper\FormWidgets;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Hgabka\NodeBundle\Entity\HasNodeInterface;
use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminConfiguratorInterface;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdminFactory;
use Hgabka\UtilsBundle\Helper\FormWidgets\FormWidget;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

class PagePartWidget extends FormWidget
{
    /**
     * @var EntityManagerInterface
     */
    protected $em;

    /**
     * @var PagePartAdminFactory
     */
    protected $pagePartAdminFactory;

    /**
     * @var PagePartAdmin
     */
    protected $pagePartAdmin;

    /**
     * @var HasNodeInterface
     */
    protected $page;

    /**
     * @var Request
     */
    protected $request;

    /**
     * @var PagePartAdminConfiguratorInterface
     */
    protected $pagePartAdminConfigurator;

    /**
     * @param HasPagePartsInterface              $page                      The page
     * @param Request                            $request                   The request
     * @param EntityManagerInterface             $em                        The entity manager
     * @param PagePartAdminConfiguratorInterface $pagePartAdminConfigurator The page part admin configurator
     * @param PagePartAdminFactory               $pagePartAdminFactory      The page part admin factory
     */
    public function __construct(HasPagePartsInterface $page, Request $request, EntityManagerInterface $em, PagePartAdminConfiguratorInterface $pagePartAdminConfigurator, PagePartAdminFactory $pagePartAdminFactory)
    {
        parent::__construct();

        $this->page = $page;
        $this->em = $em;
        $this->pagePartAdminFactory = $pagePartAdminFactory;
        $this->request = $request;
        $this->pagePartAdminConfigurator = $pagePartAdminConfigurator;

        $this->pagePartAdmin = $pagePartAdminFactory->createList($pagePartAdminConfigurator, $em, $page, null);
        $this->setTemplate('@HgabkaPagePart/FormWidgets/PagePartWidget/widget.html.twig');
    }

    /**
     * @param FormBuilderInterface $builder The form builder
     */
    public function buildForm(FormBuilderInterface $builder)
    {
        parent::buildForm($builder);

        $this->pagePartAdmin->preBindRequest($this->request);
        $this->pagePartAdmin->adaptForm($builder);
    }

    /**
     * @param Request $request
     */
    public function bindRequest(Request $request)
    {
        parent::bindRequest($request);

        $this->pagePartAdmin->bindRequest($request);
    }

    /**
     * @param EntityManager $em The entity manager
     */
    public function persist(EntityManager $em)
    {
        parent::persist($em);

        $this->pagePartAdmin->persist($this->request);
    }

    /**
     * @param FormView $formView
     *
     * @return array
     */
    public function getFormErrors(FormView $formView)
    {
        $errors = parent::getFormErrors($formView);

        $formHelper = $this->getFormHelper();

        if (isset($formView['pagepartadmin_' . $this->pagePartAdmin->getContext()])) {
            $errors = array_merge($errors, $formHelper->getRecursiveErrorMessages($formView['pagepartadmin_' . $this->pagePartAdmin->getContext()]));
        }

        return $errors;
    }

    /**
     * @return PagePartAdmin
     */
    public function getPagePartAdmin()
    {
        return $this->pagePartAdmin;
    }

    /**
     * @param Request $request
     *
     * @return array
     */
    public function getExtraParams(Request $request)
    {
        $params = [];
        $editPagePart = $request->get('edit');
        if (isset($editPagePart)) {
            $params['editpagepart'] = $editPagePart;
        }

        return $params;
    }

    /**
     * @return PagePartAdminConfiguratorInterface
     */
    public function getPagePartAdminConfigurator()
    {
        return $this->pagePartAdminConfigurator;
    }
}
