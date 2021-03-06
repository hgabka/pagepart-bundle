<?php

namespace Hgabka\PagePartBundle\Controller;

use Hgabka\PagePartBundle\Helper\HasPagePartsInterface;
use Hgabka\PagePartBundle\Helper\PagePartInterface;
use Hgabka\PagePartBundle\PagePartAdmin\PagePartAdmin;
use Hgabka\PagePartBundle\PagePartConfigurationReader\PagePartConfigurationReader;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for the pagepart administration.
 */
class PagePartAdminController extends Controller
{
    /**
     * @Route("/newPagePart", name="HgabkaPagePartBundle_admin_newpagepart")
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return array
     */
    public function newPagePartAction(Request $request)
    {
        $em = $this->get('doctrine.orm.entity_manager');

        $pageId = $request->get('pageid');
        $pageClassName = $request->get('pageclassname');
        $context = $request->get('context');
        $pagePartClass = $request->get('type');

        /** @var HasPagePartsInterface $page */
        $page = $em->getRepository($pageClassName)->find($pageId);

        if (false === $page instanceof HasPagePartsInterface) {
            throw new \RuntimeException(sprintf('Given page (%s:%d) has no pageparts', $pageClassName, $pageId));
        }

        $pagePartConfigurationReader = $this->container->get(PagePartConfigurationReader::class);
        $pagePartAdminConfigurators = $pagePartConfigurationReader->getPagePartAdminConfigurators($page);

        $pagePartAdminConfigurator = null;
        foreach ($pagePartAdminConfigurators as $ppac) {
            if ($context === $ppac->getContext()) {
                $pagePartAdminConfigurator = $ppac;
            }
        }

        if (null === $pagePartAdminConfigurator) {
            throw new \RuntimeException(sprintf('No page part admin configurator found for context "%s".', $context));
        }

        $pagePartAdmin = new PagePartAdmin($pagePartAdminConfigurator, $em, $page, $context, $this->container);
        /** @var PagePartInterface $pagePart */
        $pagePart = new $pagePartClass();

        if (false === $pagePart instanceof PagePartInterface) {
            throw new \RuntimeException(sprintf(
                'Given pagepart expected to implement PagePartInterface, %s given',
                $pagePartClass
            ));
        }

        $formFactory = $this->container->get('form.factory');
        $formBuilder = $formFactory->createBuilder(FormType::class);
        $pagePartAdmin->adaptForm($formBuilder);
        $id = 'newpp_'.time();

        $data = $formBuilder->getData();
        $data['pagepartadmin_'.$id] = $pagePart;

        $formBuilder->add('pagepartadmin_'.$id, $pagePart->getDefaultAdminType(), ['config' => $pagePartAdmin->getConfig()]);
        $formBuilder->setData($data);
        $form = $formBuilder->getForm();
        $formview = $form->createView();
        $extended = $this->getParameter('hgabka_page_part.extended');

        return $this->render('@HgabkaPagePart/PagePartAdminTwigExtension/pagepart.html.twig', [
            'id' => $id,
            'form' => $formview,
            'pagepart' => $pagePart,
            'pagepartadmin' => $pagePartAdmin,
            'page' => $pagePartAdmin->getPage(),
            'editmode' => true,
            'extended' => $extended,
        ]);
    }
}
