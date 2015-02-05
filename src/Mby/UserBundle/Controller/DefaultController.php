<?php

namespace Mby\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DefaultController extends Controller
{
    /**
     * @Route("/hello/{name}")
     * @Template()
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction($name)
    {
        return array('name' => $name);
    }
}
