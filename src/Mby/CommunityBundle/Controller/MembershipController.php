<?php

namespace Mby\CommunityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Mby\UserBundle\Entity\User;
use Mby\UserBundle\Service\MembershipManager;

class MembershipController extends Controller
{

	/**
	 * Register a user's membership to a community.
	 *
     * @Route("/membership/apply")
     * @Security("has_role('ROLE_USER')")
     */
    public function applyAction(Community $community)
    {
     
        $msManager = $this->get(MembershipManager::NAME);
        $msManager->create();

        return array('name' => $name);

    }

    protected function 


}
