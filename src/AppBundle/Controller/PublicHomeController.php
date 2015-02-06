<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class PublicHomeController extends Controller
{
    /**
     * @Route("/")
     */
    public function indexAction()
    {
    	
        return $this->render('AppBundle:PublicHome:index.html.twig', array(
            
        ));
    }

}
