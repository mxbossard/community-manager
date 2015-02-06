<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BreadcrumbController extends Controller
{
    /**
     * @Route("/render")
     */
    public function renderAction()
    {
        $items = array();
        $items[] = 'app_publichome_index';

        $request = $this->container->get('request');
		$routeName = $request->get('_route');

		//$items[] = $routeName;

        return $this->render('AppBundle:Breadcrumb:render.html.twig', array(
            'items' => $items
        ));  
    }

}
