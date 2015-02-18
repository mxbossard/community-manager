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
 * Class CommunityAdminController
 * @package Mby\CommunityBundle\Controller
 *
 * @Route("/administrateCommunity")
 */
class CommunityAdminController extends Controller
{

	/**
	 * Display community administration index
	 *
     * @Route("/{id}", name="community_administrate")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Community $community)
    {
        $user= $this->get('security.context')->getToken()->getUser();

        return new Response($community->getName());
    }

}
