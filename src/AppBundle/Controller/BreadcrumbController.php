<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BreadcrumbController extends Controller
{
    /**
     * @Route("/render")
     */
    public function renderAction(Request $request)
    {
        $items = array();
        $items[] = 'app_publichome_index';

		$currentRoute = $request->attributes->get('_route');
        $currentUri = $this->getRequest()->getUri();

		//$items[] = $currentUri;

        return $this->render('AppBundle:Breadcrumb:render.html.twig', array(
            'items' => $items
        ));  
    }

}
