<?php

namespace Mby\CommunityBundle\Controller;

use AppBundle\Controller\AbstractController;
use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Form\Type\ActionType;
use Mby\CommunityBundle\Service\Facade\MembershipFacade;
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
class MembershipController extends AbstractController
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
        $this->assertValidCrsf($request, "apply");

        $seasonId = $request->get('id');

        $season = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Season')
            ->find($seasonId);

        /** @var MembershipFacade $msFacade */
        $msFacade = $this->get(MembershipFacade::SERVICE_NAME);
        $user= $this->get('security.context')->getToken()->getUser();

        $msFacade->applyToSeason($user, $season);

        return $this->redirectToRoute("lobby_myMemberships");
    }

    /**
     * Validate an application to a community.
     *
     * @Route("/acceptApplication", name="membership_acceptApplication")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function acceptApplicationAction(Request $request)
    {
        $this->assertValidCrsf($request, "valid-application");

        $membership = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Membership')
            ->find(array(
                "user" => $request->get("user_id"),
                "season" => $request->get("season_id"),
            ));

        $user= $this->get('security.context')->getToken()->getUser();

        /** @var MembershipFacade $msFacade */
        $msFacade = $this->get(MembershipFacade::SERVICE_NAME);
        $msFacade->acceptApplication($user, $membership);

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
        $this->assertValidCrsf($request, "cancel-apply");

        $membership = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Membership')
            ->find(array(
                "id" => $request->get("id"),
            ));

        $user= $this->get('security.context')->getToken()->getUser();

        /** @var MembershipFacade $msFacade */
        $msFacade = $this->get(MembershipFacade::SERVICE_NAME);
        $msFacade->cancelApplication($user, $membership);

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
            'communities' => $communities,
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
