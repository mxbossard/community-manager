<?php

namespace Mby\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\HasLifecycleCallbacks()
 */
class User extends BaseUser
{
    /**
     * @var integer
     *
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created", type="datetime")
     */
    private $created;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="updated", type="datetime", nullable=true)
     */
    private $updated;

    /**
     * @var \ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Mby\CommunityBundle\Entity\Community", mappedBy="owner")
     */
    private $ownedCommunities;

    /**
     * @var \ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Mby\CommunityBundle\Entity\Membership", mappedBy="community")
     */
    private $memberships;

    public function __construct()
    {
        parent::__construct();
        
        $this->ownedCommunities = new ArrayCollection();
        $this->memberships = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersistCallback()
    {
        $this->created = new \DateTime();
    }

    /**
     * @ORM\PreUpdate
     */
    public function preUpdateCallback()
    {
        $this->updated = new \DateTime();
    }


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     * @return User
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime 
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     * @return User
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime 
     */
    public function getUpdated()
    {
        return $this->updated;
    }


    /**
     * Add ownedCommunities
     *
     * @param \Mby\CommunityBundle\Entity\Community $ownedCommunities
     * @return User
     */
    public function addOwnedCommunity(\Mby\CommunityBundle\Entity\Community $ownedCommunities)
    {
        $this->ownedCommunities[] = $ownedCommunities;

        return $this;
    }

    /**
     * Remove ownedCommunities
     *
     * @param \Mby\CommunityBundle\Entity\Community $ownedCommunities
     */
    public function removeOwnedCommunity(\Mby\CommunityBundle\Entity\Community $ownedCommunities)
    {
        $this->ownedCommunities->removeElement($ownedCommunities);
    }

    /**
     * Get ownedCommunities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOwnedCommunities()
    {
        return $this->ownedCommunities;
    }

    /**
     * Add memberships
     *
     * @param \Mby\CommunityBundle\Entity\Membership $memberships
     * @return User
     */
    public function addMembership(\Mby\CommunityBundle\Entity\Membership $memberships)
    {
        $this->memberships[] = $memberships;

        return $this;
    }

    /**
     * Remove memberships
     *
     * @param \Mby\CommunityBundle\Entity\Membership $memberships
     */
    public function removeMembership(\Mby\CommunityBundle\Entity\Membership $memberships)
    {
        $this->memberships->removeElement($memberships);
    }

    /**
     * Get memberships
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMemberships()
    {
        return $this->memberships;
    }
}
