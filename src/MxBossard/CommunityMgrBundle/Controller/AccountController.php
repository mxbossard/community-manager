<?php

namespace MxBossard\CommunityMgrBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use MxBossard\CommunityMgrBundle\Form\Type\RegistrationType;
use MxBossard\CommunityMgrBundle\Form\Model\Registration;

class AccountController extends Controller
{

    /**
     * @Route("/register")
     * @Method("GET")
     * @Template()
     */
    public function registerAction()
    {
        $registration = new Registration();
        $form = $this->createForm(new RegistrationType(), $registration, array(
            'action' => $this->generateUrl('mxbossard_communitymgr_account_create'),
        ));

        return $this->render(
            'MxBossardCommunityMgrBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @Route("/register/create")
     * @Method("POST")
     * @Template()
     */
    public function createAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new RegistrationType(), new Registration());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $registration = $form->getData();

            $em->persist($registration->getUser());
            $em->flush();

            return $this->redirect($this->generateUrl('homepage'));
        }

        return $this->render(
            'MxBossardCommunityMgrBundle:Account:register.html.twig',
            array('form' => $form->createView())
        );
    }
}