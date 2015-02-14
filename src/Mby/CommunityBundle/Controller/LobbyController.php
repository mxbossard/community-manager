<?php

namespace Mby\CommunityBundle\Controller;

use Mby\CommunityBundle\Entity\Membership;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Service\MembershipManager;

/**
 * Class LobbyController
 * @package Mby\CommunityBundle\Controller
 *
 * @Route("/lobby")
 */
class LobbyController extends Controller
{

	/**
	 * Display user current memberships.
	 *
     * @Route("/myMemberships", name="lobby_myMemberships")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function myMembershipsAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $mySeasons = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Season')
            ->findUserActiveSeasons($user);

        return $this->render('MbyCommunityBundle:Lobby:myMemberships.html.twig', array(
            'mySeasons' => $mySeasons
        ));
    }

    /**
     * Display user old memberships.
     *
     * @Route("/oldMemberships", name="lobby_oldMemberships")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function oldMembershipsAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $oldSeasons = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Season')
            ->findUserInactiveSeasons($user);

        return $this->render('MbyCommunityBundle:Lobby:oldMemberships.html.twig', array(
            'mySeasons' => $oldSeasons
        ));
    }

    /**
     * Search for communities.
     *
     * @Route("/searchCommunities", name="lobby_searchCommunities")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function searchCommunitiesAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $communities = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Community')
            ->findJoinableCommunities();

        return $this->render('MbyCommunityBundle:Lobby:searchCommunities.html.twig', array(
            'communities' => $communities
        ));
    }

    /**
     * Display communities which user is moderator.
     *
     * @Route("/moderation", name="lobby_moderation")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function moderationAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $myCommunities = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Community')
            ->findUserOwnedCommunities($user);

        return $this->render('MbyCommunityBundle:Lobby:moderation.html.twig', array(
            'myCommunities' => $myCommunities
        ));
    }

    /**
     * Display communities which user is administrator.
     *
     * @Route("/administration", name="lobby_administration")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function administrationAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $myCommunities = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Community')
            ->findUserOwnedCommunities($user);

        return $this->render('MbyCommunityBundle:Lobby:administration.html.twig', array(
            'myCommunities' => $myCommunities
        ));
    }

    /**
     * Display user owned communities.
     *
     * @Route("/ownership", name="lobby_ownership")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function ownershipAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $myCommunities = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Community')
            ->findUserOwnedCommunities($user);

        return $this->render('MbyCommunityBundle:Lobby:ownership.html.twig', array(
            'myCommunities' => $myCommunities
        ));
    }

}
