<?php

namespace Mby\CommunityBundle\Controller;

use AppBundle\Controller\AbstractController;
use Doctrine\ORM\NoResultException;
use Mby\CommunityBundle\Entity\Membership;
use Mby\CommunityBundle\Entity\MembershipRepository;
use Mby\CommunityBundle\Entity\SeasonRepository;
use Mby\CommunityBundle\Form\Type\AdminSeasonType;
use Mby\CommunityBundle\Service\Facade\MembershipFacade;
use Mby\CommunityBundle\Service\Facade\SeasonFacade;
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
 * @Route("/communityAdmin")
 */
class CommunityAdminController extends AbstractController
{

	/**
	 * Display community administration index.
	 *
     * @Route("/{id}", name="community_admin")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Community $community
     * @return Response
     */
    public function indexAction(Community $community) {
        $this->assertAdmin($community);

        /** @var SeasonRepository $seasonRepo */
        $seasonRepo = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Season');

        /** @var MembershipRepository $msRepo */
        $msRepo = $this->getDoctrine()
            ->getRepository('MbyCommunityBundle:Membership');

        try {
            $currentSeason = $seasonRepo->findCurrentSeason($community);
            $currentApplications = $msRepo->findApplications($currentSeason);
        } catch (NoResultException $e) {
            $currentSeason = null;
            $currentApplications = null;
        }

        $oldSeasons = $seasonRepo->findOldSeasons($community);
        $closeSeasonFormView = null;

        if ($currentSeason !== null && $currentSeason->getToDate() === null) {
            // If current season include form to close the season
            $closeSeasonForm = $this->buildCloseSeasonForm(
                array(
                    'endDate' => new \DateTime('today')
                ), array(
                    'action' => $this->generateUrl('community_admin-close_season',
                        array('id' => $currentSeason->getId(),)
                    )
                )
            );
            $closeSeasonFormView = $closeSeasonForm->createView();
        }

        return $this->render('MbyCommunityBundle:Lobby/admin:community_admin.html.twig', array(
            'community' => $community,
            'currentSeason' => $currentSeason,
            'currentApplications' => $currentApplications,
            'oldSeasons' => $oldSeasons,
            'closeSeasonForm' => $closeSeasonFormView,
        ));
    }

    /**
     * Display new season form.
     *
     * @Route("/newSeason/{id}/{_token}", name="community_admin-new_season")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Community $community
     * @param string $_token
     * @return Response
     */
    public function newSeasonAction(Community $community, $_token) {
        $this->assertValidCsrfToken('new_season', $_token);
        $this->assertAdmin($community);

        /** @var SeasonFacade $seasonFacade */
        $seasonFacade = $this->get(SeasonFacade::SERVICE_NAME);
        $season = $seasonFacade->buildNewSeason($community);

        $form = $this->createForm('admin_season', $season, array(
            'action' => $this->generateUrl('community_admin-save_season', array(
                            'id' => $community->getId(),
                        )),
            'intention' => AdminSeasonType::EDIT,
        ));

        return $this->render('MbyCommunityBundle:Lobby/admin:season_admin.html.twig', array(
            'season' => $season,
            'form' => $form->createView(),
        ));
    }

    /**
     * Display edit season form.
     *
     * @Route("/editSeason/{id}/{_token}", name="community_admin-edit_season")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Season $season
     * @param string $_token
     * @return Response
     */
    public function editSeasonAction(Season $season, $_token) {
        $this->assertValidCsrfToken('edit_season', $_token);
        $community = $season->getCommunity();
        $this->assertAdmin($community);

        $form = $this->createForm('admin_season', $season, array(
            'action' => $this->generateUrl('community_admin-save_season', array(
                    'id' => $community->getId(),
                )),
            'intention' => AdminSeasonType::EDIT,
        ));

        return $this->render('MbyCommunityBundle:Lobby/admin:season_admin.html.twig', array(
            'season' => $season,
            'form' => $form->createView(),
        ));
    }

    /**
     * Save a season.
     *
     * @Route("/saveSeason/{id}", name="community_admin-save_season")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     *
     * @return Response
     */
    public function saveSeasonAction(Community $community, Request $request) {
        $form = $this->createForm('admin_season');
        $form->handleRequest($request);

        /** @var Season $season */
        $season = $form->getData();

        /** @var SeasonFacade $seasonFacade */
        $seasonFacade = $this->get(SeasonFacade::SERVICE_NAME);
        if ($season->getId() === null) {
            $seasonFacade->createNewSeason($this->currentUser(), $community, $season);
        } else {
            $seasonFacade->updateSeason($this->currentUser(), $season);
        }

        return $this->redirectToRoute('community_admin', array(
            'id' => $community->getId()
        ));
    }

    /**
     * Lock a season.
     *
     * @Route("/lockSeason/{id}/{_token}", name="community_admin-lock_season")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Season $season
     * @param string $_token
     * @return Response
     */
    public function lockSeasonAction(Season $season, $_token) {
        $this->assertValidCsrfToken('lock_season', $_token);

        /** @var SeasonFacade $seasonFacade */
        $seasonFacade = $this->get(SeasonFacade::SERVICE_NAME);
        $seasonFacade->lockSeason($this->currentUser(), $season);

        return $this->redirectToRoute('community_admin', array(
            'id' => $season->getCommunity()->getId()
        ));
    }

    /**
     * Unlock a season.
     *
     * @Route("/unlockSeason/{id}/{_token}", name="community_admin-unlock_season")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Season $season
     * @param string $_token
     * @return Response
     */
    public function unlockSeasonAction(Season $season, $_token) {
        $this->assertValidCsrfToken('unlock_season', $_token);

        /** @var SeasonFacade $seasonFacade */
        $seasonFacade = $this->get(SeasonFacade::SERVICE_NAME);
        $seasonFacade->unlockSeason($this->currentUser(), $season);

        return $this->redirectToRoute('community_admin', array(
            'id' => $season->getCommunity()->getId()
        ));
    }

    /**
     * Close a season.
     *
     * @Route("/closeSeason/{id}", name="community_admin-close_season")
     * @Method("POST")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Season $season
     * @param string $_token
     * @return Response
     */
    public function closeSeasonAction(Season $season, Request $request) {
        $form = $this->buildCloseSeasonForm();
        $form->handleRequest($request);

        $endDate = $form->getData()['endDate'];

        /** @var SeasonFacade $seasonFacade */
        $seasonFacade = $this->get(SeasonFacade::SERVICE_NAME);
        $seasonFacade->closeSeason($this->currentUser(), $season, $endDate);

        return $this->redirectToRoute('community_admin', array(
            'id' => $season->getCommunity()->getId()
        ));
    }

    /**
     * Accept an application.
     *
     * @Route("/acceptApplication/{id}/{_token}", name="community_admin-accept_application")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Membership $membership
     * @param $_token
     * @return Response
     */
    public function acceptApplicationAction(Membership $membership, $_token) {
        $this->assertValidCsrfToken('accept_application', $_token);

        /** @var MembershipFacade $msFacade */
        $msFacade = $this->get(MembershipFacade::SERVICE_NAME);
        $msFacade->acceptApplication($this->currentUser(), $membership);

        return $this->redirectToRoute('community_admin', array(
            'id' => $membership->getSeason()->getCommunity()->getId()
        ));
    }

    /**
     * Reject an application.
     *
     * @Route("/rejectApplication/{id}/{_token}", name="community_admin-reject_application")
     * @Method("GET")
     * @Security("has_role('ROLE_USER')")
     *
     * @param Membership $membership
     * @param $_token
     * @return Response
     */
    public function rejectApplicationAction(Membership $membership, $_token) {
        $this->assertValidCsrfToken('reject_application', $_token);

        /** @var MembershipFacade $msFacade */
        $msFacade = $this->get(MembershipFacade::SERVICE_NAME);
        $msFacade->rejectApplication($this->currentUser(), $membership);

        return $this->redirectToRoute('community_admin', array(
            'id' => $membership->getSeason()->getCommunity()->getId()
        ));
    }

    /**
     * @param $currentSeason
     * @return \Symfony\Component\Form\Form
     */
    public function buildCloseSeasonForm($data = null, array $options = array())
    {
        $closeSeasonForm = $this->createFormBuilder($data, $options)
            ->add('endDate', 'date')
            ->getForm();

        return $closeSeasonForm;
    }

}
