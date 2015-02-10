<?php

namespace Mby\CommunityBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

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

    /**
     * Register a user's membership to a community.
     *
     * @Route("/myCommunities", name="myCommunities")
     * @Security("has_role('ROLE_USER')")
     */
    public function myCommunitiesAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $communities = $this->getDoctrine()
        ->getRepository('MbyCommunityBundle:Membership')
        ->findAllUserCommunities($user);

        return $this->render('MbyCommunityBundle:Membership:myCommunities.html.twig', array(
            'communities' => $communities
        ));

    }

    /**
     * Register a user's membership to a community.
     *
     * @Route("/allCommunities", name="allCommunities")
     * @Security("has_role('ROLE_USER')")
     */
    public function communitiesAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $communities = $this->getDoctrine()
        ->getRepository('MbyCommunityBundle:Membership')
        ->findAllCommunities();

        return $this->render('MbyCommunityBundle:Membership:myCommunities.html.twig', array(
            'communities' => $communities
        ));

    }

}
