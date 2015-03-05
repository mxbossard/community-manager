<?php

namespace AppBundle\Controller;

use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Service\CsrfIntentRepository;
use Mby\CommunityBundle\Service\PrivilegeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\User;

abstract class AbstractController extends Controller
{

    const CSRF_FIELD_NAME = "_token";

    const CSRF_INTENT_NAME = "intent";

    /**
     * @param $intent
     * @param $token
     * @throws \Exception
     */
    public function assertValidCsrfToken($intent, $token)
    {
        $csrfProvider = $this->get('form.csrf_provider');

        if (!$csrfProvider->isCsrfTokenValid($intent, $token)) {
            throw new \Exception("CSRF token invalid !");
        }
    }

    /**
     * @param $intention
     * @param $token
     * @throws \Exception
     */
    public function assertValidMbyCsrf($intention, $token)
    {
        $csrfProvider = $this->get('form.csrf_provider');

        /** @var CsrfIntentRepository $csrfIntentRepo */
        $csrfIntentRepo = $this->get(CsrfIntentRepository::SERVICE_NAME);

        $intent = $csrfIntentRepo->get($this->currentUser(), $intention);

        if (!$csrfProvider->isCsrfTokenValid($intent, $token)) {
            throw new \Exception("CSRF token invalid !");
        }
    }

    /**
     * @param Request $request
     * @param $intent
     * @throws \Exception
     */
    public function assertValidCsrfRequest($intent, Request $request) {
        $token = $request->get(AbstractController::CSRF_FIELD_NAME);

        $this->assertValidCsrfToken($intent, $token);
    }

    /**
     * Assert current user is owner of the community.
     *
     * @param Community $community
     */
    public function assertOwner(Community $community) {
        /** @var PrivilegeManager $privilegeManager */
        $privilegeManager = $this->get(PrivilegeManager::SERVICE_NAME);
        if (! $privilegeManager->isOwner($this->currentUser(), $community)) {
            throw new AccessDeniedException("not community owner");
        }
    }

    /**
     * Assert current user is admin of the community.
     *
     * @param Community $community
     */
    public function assertAdmin(Community $community) {
        /** @var PrivilegeManager $privilegeManager */
        $privilegeManager = $this->get(PrivilegeManager::SERVICE_NAME);
        if (! $privilegeManager->isAdministrator($this->currentUser(), $community)) {
            throw new AccessDeniedException("not community administrator");
        }
    }

    /**
     * Assert current user is moderator of the community.
     *
     * @param Community $community
     */
    public function assertModerator(Community $community) {
        /** @var PrivilegeManager $privilegeManager */
        $privilegeManager = $this->get(PrivilegeManager::SERVICE_NAME);
        if (! $privilegeManager->isModerator($this->currentUser(), $community)) {
            throw new AccessDeniedException("not community moderator");
        }
    }

    /**
     * @return User
     */
    public function currentUser() {
        return $this->get('security.context')->getToken()->getUser();
    }

    public function render($view, array $parameters = array(), Response $response = null)
    {
        $user = $this->currentUser();

        /** @var CsrfIntentRepository $csrfIntentRepo */
        $csrfIntentRepo = $this->get(CsrfIntentRepository::SERVICE_NAME);
        $intent = $csrfIntentRepo->generate($user, 'foo');

        $parameters[AbstractController::CSRF_INTENT_NAME] = $intent;

        return parent::render($view, $parameters, $response);
    }


}