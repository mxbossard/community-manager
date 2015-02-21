<?php

namespace Mby\CommunityBundle\Controller;

use AppBundle\Controller\AbstractController;
use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Form\Type\ManageCommunityType;
use Mby\CommunityBundle\Service\Facade\CommunityFacade;
use Mby\CommunityBundle\Service\PrivilegeManager;
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
class CommunityManagerController extends AbstractController
{

	/**
	 * Display community managing index.
	 *
     * @Route("/{id}", name="community-manage")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     */
    public function indexAction(Community $community)
    {
        $this->assertOwner($community);

        $privilegedUsers = $this->get(CommunityFacade::SERVICE_NAME)->findCommunityPrivilegedUsers($community);

        $form = $this->createForm('manage_community', array(
            'community' => $community,
            'privileges' => $community->getPrivileges(),
            'privilegedUsers' => $privilegedUsers,
        ), array(
            'action' => $this->generateUrl('community-manage_save'),
        ));

        return $this->render('MbyCommunityBundle:Lobby:manageCommunity.html.twig', array(
            "form" => $form->createView(),
            'privilegedUsers' => $privilegedUsers,
        ));
    }

    /**
     * Add new owner.
     *
     * @Route("/save", name="community-manage_save")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     */
    public function saveAction(Request $request)
    {
        $form = $this->createForm('manage_community');
        $form->handleRequest($request);

        $community = $form['community']->getData();
        $privilegedUsers = $form['privilegedUsers']->getData();

        if ($form->isValid()) {
            $communityId = $community->getId();

            $community = $this->getDoctrine()
                ->getRepository('MbyCommunityBundle:Community')
                ->find($communityId);

            $this->assertOwner($community);

            /** @var CommunityFacade $communityFacade */
            $communityFacade = $this->get(CommunityFacade::SERVICE_NAME);
            $communityFacade->saveCommunityWithPrivileges($this->currentUser(), $community, $privilegedUsers);

            return $this->redirectToRoute('community-manage', array(
                'id' => $communityId,
            ));
        }

        return $this->render('MbyCommunityBundle:Lobby:manageCommunity.html.twig', array(
            "form" => $form->createView(),
            'privilegedUsers' => $privilegedUsers,
        ));

    }

}
