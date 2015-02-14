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
 * Class MembershipController
 * @package Mby\CommunityBundle\Controller
 *
 * @Route("/membership")
 */
class MembershipController extends Controller
{

	/**
	 * Register a user application to a community.
	 *
     * @Route("/apply", name="membership_apply")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function applyAction(Request $request)
    {
        $season = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Season')
            ->find($request->get("season_id"));

        $msManager = $this->get(MembershipManager::SERVICE_NAME);
        $user= $this->get('security.context')->getToken()->getUser();

        $msManager->apply($user, $season, null, null);

        return $this->redirectToRoute("lobby_myMemberships");
    }

    /**
     * Validate an application to a community.
     *
     * @Route("/validApplication", name="membership_validApplication")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function validApplicationAction(Request $request)
    {
        $membership = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Membership')
            ->find(array(
                "user" => $request->get("user_id"),
                "season" => $request->get("season_id"),
            ));

        $user= $this->get('security.context')->getToken()->getUser();

        $msManager = $this->get(MembershipManager::SERVICE_NAME);
        $msManager->validApplication($user, $membership);

        return $this->redirectToRoute("lobby_myMemberships");
    }

    /**
     * Cancel an application to a community.
     *
     * @Route("/cancelApplication", name="membership_cancelApplication")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function cancelApplicationAction(Request $request)
    {
        $membership = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Membership')
            ->find(array(
                "user" => $request->get("user_id"),
                "season" => $request->get("season_id"),
            ));

        $user= $this->get('security.context')->getToken()->getUser();

        $msManager = $this->get(MembershipManager::SERVICE_NAME);
        $msManager->cancelApplication($user, $membership);

        return $this->redirectToRoute("lobby_myMemberships");
    }

    /**
     * Display the list of communities in which I am member.
     *
     * @Route("/myCommunities", name="membership_myCommunities")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function myCommunitiesAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $communities = $this->getDoctrine()
        ->getRepository('MbyCommunityBundle:Community')
        ->findAllUserCommunities($user);

        return $this->render('MbyCommunityBundle:Membership:myCommunities.html.twig', array(
            'communities' => $communities
        ));

    }

    /**
     * Display the list of all communities.
     *
     * @Route("/allCommunities", name="membership_allCommunities")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function allCommunitiesAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $communities = $this->getDoctrine()
        ->getRepository('MbyCommunityBundle:Community')
        ->findAllCommunities($user);

        return $this->render('MbyCommunityBundle:Membership:myCommunities.html.twig', array(
            'communities' => $communities
        ));

    }

    /**
     * Display the list of seasons in which I am member.
     *
     * @Route("/mySeasons", name="membership_mySeasons")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function mySeasonsAction()
    {
        $user= $this->get('security.context')->getToken()->getUser();

        $seasons = $this->getDoctrine()
        ->getRepository('MbyCommunityBundle:Season')
        ->findAllUserSeasons($user);

        return $this->render('MbyCommunityBundle:Membership:mySeasons.html.twig', array(
            'seasons' => $seasons
        ));

    }
}
