<?php

namespace Mby\CommunityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * CommunityPrivilege
 *
 * @ORM\Table(name="mby_communities_privileges")
 * @ORM\Entity
 */
class CommunityPrivilege
{

    /**
     * @var \Mby\UserBundle\Entity\User
     * 
     * @ORM\Id 
     * @ORM\ManyToOne(targetEntity="Mby\UserBundle\Entity\User", inversedBy="privileges")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var \Mby\CommunityBundle\Entity\Community
     * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Mby\CommunityBundle\Entity\Community", inversedBy="privileges")
     * @ORM\JoinColumn(name="community_id", referencedColumnName="id", nullable=false)
     */
    private $community;

    /**
     * @var \Mby\CommunityBundle\Entity\Privilege
     * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Mby\CommunityBundle\Entity\Privilege")
     * @ORM\JoinColumn(name="privilege_code", referencedColumnName="code", nullable=false)
     */
    private $privilege;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * Set note
     *
     * @param string $note
     * @return CommunityPrivilege
     */
    public function setNote($note)
    {
        $this->note = $note;

        return $this;
    }

    /**
     * Get note
     *
     * @return string 
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Set user
     *
     * @param \Mby\UserBundle\Entity\User $user
     * @return CommunityPrivilege
     */
    public function setUser(\Mby\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Mby\UserBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set community
     *
     * @param \Mby\CommunityBundle\Entity\Community $community
     * @return CommunityPrivilege
     */
    public function setCommunity(\Mby\CommunityBundle\Entity\Community $community)
    {
        $this->community = $community;

        return $this;
    }

    /**
     * Get community
     *
     * @return \Mby\CommunityBundle\Entity\Community 
     */
    public function getCommunity()
    {
        return $this->community;
    }

    /**
     * Set privilege
     *
     * @param \Mby\CommunityBundle\Entity\Privilege $privilege
     * @return CommunityPrivilege
     */
    public function setPrivilege(\Mby\CommunityBundle\Entity\Privilege $privilege)
    {
        $this->privilege = $privilege;

        return $this;
    }

    /**
     * Get privilege
     *
     * @return \Mby\CommunityBundle\Entity\Privilege 
     */
    public function getPrivilege()
    {
        return $this->privilege;
    }
}
