<?php

namespace AppBundle\Controller;

use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Service\PrivilegeManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\User\User;

abstract class AbstractController extends Controller
{

    const CSRF_FIELD_NAME = "_token";

    /**
     * @param Request $request
     * @param $intent
     * @throws \Exception
     */
    public function assertValidCrsf(Request $request, $intent) {
        $csrfProvider = $this->get('form.csrf_provider');
        if (! $csrfProvider->isCsrfTokenValid($intent, $request->get(AbstractController::CSRF_FIELD_NAME))) {
            throw new \Exception("CSRF token invalid !");
        }
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
}