<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\PrivilegeRepository;

class CsrfIntentRepository
{

    const SERVICE_NAME = 'csrf_intent_repository';

    /**
     * @var array
     */
    protected $repo = array();

    public function __construct()
    {
        //$this->repo = array();
    }

    /**
     * Get a user intent.
     *
     * @param User $user
     * @param $intention
     * @return mixed
     * @throws \Exception
     */
    public function get(User $user, $intention) {
        $username = $user->getUsername();
        dump($this->repo);
        if (! isset($this->repo[$username])) {
            throw new \Exception(sprintf("intent not previously generated for user %s", $username));
        }

        if (! isset($this->repo[$username][$intention])) {
            throw new \Exception(sprintf("intent not previously generated for intention %s", $intention));
        }

        return $this->repo[$username][$intention];
    }

    /**
     * Generate a user intent.
     *
     * @param User $user
     * @param $intention
     * @return mixed
     */
    public function generate(User $user, $intention) {
        $intent = $intention . $user->getUsername();

        $this->store($user, $intention, $intent);

        return $intent;
    }

    /**
     * Store the user intent.
     *
     * @param User $user
     * @param $intention
     * @param $intent
     */
    protected function store(User $user, $intention, $intent) {
        $username = $user->getUsername();
        if (! isset($this->repo[$username])) {
            $this->repo[$username] = array();
        }

        $this->repo[$username][$intention] = $intent;
    }

}
