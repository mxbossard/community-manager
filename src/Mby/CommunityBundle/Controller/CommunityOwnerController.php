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
use Symfony\Component\HttpFoundation\Response;

/**
 * Class CommunityOwnerController
 * @package Mby\CommunityBundle\Controller
 *
 * @Route("/manageCommunity")
 */
class CommunityOwnerController extends Controller
{

	/**
	 * Display community managing index.
	 *
     * @Route("/{id}", name="community_manage")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Community $community)
    {
        $user= $this->get('security.context')->getToken()->getUser();

        return new Response($community->getName());
    }

    /**
     * Add new owner.
     *
     * @Route("/newOwner", name="community_newOwner")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function newOwnerAction(Request $request)
    {
        $user= $this->get('security.context')->getToken()->getUser();

        return new Response($community->getName());
    }

    /**
     * Add new admin.
     *
     * @Route("newAdmin/{communityId}/{userId}", name="community_newOwner")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function newAdminAction(Request $request)
    {
        $user= $this->get('security.context')->getToken()->getUser();

        return new Response($community->getName());
    }

}
