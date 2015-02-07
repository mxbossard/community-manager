<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class NavigationController extends Controller
{
    /**
     * @Route("/listNavigableLinks")
     */
    public function listNavigableLinksAction()
    {
        $links = array();
        $links[] = 'users';
        $links[] = 'communities';

        return $this->render('AppBundle:Navigation:listNavigableLinks.html.twig', array(
            'links' => $links 
        ));
  
    }

}