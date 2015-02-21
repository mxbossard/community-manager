<?php

namespace Mby\CommunityBundle\Model;

use Mby\CommunityBundle\Entity\Community;
use Mby\UserBundle\Entity\User;

/**
 * PrivilegedUser
 *
 */
class PrivilegedUser
{
    /**
     * @var User
     *
     */
    private $user;

    /**
     * @var Community
     *
     */
    private $community;

    /**
     * @var string
     *
     */
    private $label;

    /**
     * @var integer
     *
     */
    private $userId;

    /**
     * @var integer
     *
     */
    private $communityId;

    /**
     * @var boolean
     *
     */
    private $owner;

    /**
     * @var boolean
     *
     */
    private $admin;

    /**
     * @var boolean
     *
     */
    private $moderator;

    function __construct()
    {
        $this->owner = false;
        $this->admin = false;
        $this->moderator = false;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }

    /**
     * @param string $label
     */
    public function setLabel($label)
    {
        $this->label = $label;
    }

    /**
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;
    }

    /**
     * @return int
     */
    public function getCommunityId()
    {
        return $this->communityId;
    }

    /**
     * @param int $communityId
     */
    public function setCommunityId($communityId)
    {
        $this->communityId = $communityId;
    }

    /**
     * @return boolean
     */
    public function isOwner()
    {
        return $this->owner;
    }

    /**
     * @param boolean $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return boolean
     */
    public function isAdmin()
    {
        return $this->admin;
    }

    /**
     * @param boolean $admin
     */
    public function setAdmin($admin)
    {
        $this->admin = $admin;
    }

    /**
     * @return boolean
     */
    public function isModerator()
    {
        return $this->moderator;
    }

    /**
     * @param boolean $moderator
     */
    public function setModerator($moderator)
    {
        $this->moderator = $moderator;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @return Community
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * @param Community $community
     */
    public function setCommunity($community)
    {
        $this->community = $community;
    }

}
