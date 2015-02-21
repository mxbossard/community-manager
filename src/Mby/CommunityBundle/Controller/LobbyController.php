<?php

namespace Mby\CommunityBundle\Controller;

use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Form\Type\ActionType;
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

        $myMemberships = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Membership')
            ->findUserActiveMemberships($user);

        return $this->render('MbyCommunityBundle:Lobby:myMemberships.html.twig', array(
            'memberships' => $myMemberships,
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

        $oldMemberships = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Membership')
            ->findUserOldMemberships($user);

        return $this->render('MbyCommunityBundle:Lobby:oldMemberships.html.twig', array(
            'memberships' => $oldMemberships
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

        $seasons = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Season')
            ->findUserApplicableSeasons($user);

        return $this->render('MbyCommunityBundle:Lobby:searchCommunities.html.twig', array(
            'seasons' => $seasons,
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

        $privileges = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:CommunityPrivilege')
            ->findUserModeratorPrivileges($user);

        return $this->render('MbyCommunityBundle:Lobby:moderation.html.twig', array(
            'privileges' => $privileges
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

        $privileges = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:CommunityPrivilege')
            ->findUserAdminPrivileges($user);

        return $this->render('MbyCommunityBundle:Lobby:administration.html.twig', array(
            'privileges' => $privileges
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

        $privileges = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:CommunityPrivilege')
            ->findUserOwnerPrivileges($user);

        return $this->render('MbyCommunityBundle:Lobby:ownership.html.twig', array(
            'privileges' => $privileges
        ));
    }

}
