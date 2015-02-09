<?php

namespace Mby\CommunityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Membership
 *
 * @ORM\Table(name="mby_memberships")
 * @ORM\Entity(repositoryClass="Mby\CommunityBundle\Entity\MembershipRepository")
 */
class Membership extends AbstractBaseEntity
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    //private $id;

    /**
     * @var Mby\UserBundle\Entity\User
     * 
     * @ORM\Id 
     * @ORM\ManyToOne(targetEntity="Mby\UserBundle\Entity\User", inversedBy="memberships")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $user;

    /**
     * @var Mby\CommunityBundle\Entity\Community
     * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Mby\CommunityBundle\Entity\Community", inversedBy="memberships")
     * @ORM\JoinColumn(name="community_id", referencedColumnName="id", nullable=false)
     */
    //private $community;

    /**
     * @var Mby\CommunityBundle\Entity\Season
     * 
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Mby\CommunityBundle\Entity\Season", inversedBy="memberships")
     * @ORM\JoinColumn(name="season_id", referencedColumnName="id", nullable=false)
     */
    private $season;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fromDate", type="date", nullable=true)
     */
    private $fromDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="toDate", type="date", nullable=true)
     */
    private $toDate;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isAdministrator", type="boolean")
     */
    private $isAdministrator;

    /**
     * @var boolean
     *
     * @ORM\Column(name="isModerator", type="boolean")
     */
    private $isModerator;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var \ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Mby\CommunityBundle\Entity\Responsibility")
     * @ORM\JoinTable(name="mby_memberships_responsibilities",
     *      joinColumns={
     *               @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", nullable=false, unique=true),
     *               @ORM\JoinColumn(name="season_id", referencedColumnName="season_id", nullable=false, unique=true)
     *           },
     *      inverseJoinColumns={@ORM\JoinColumn(name="responsibility_id", referencedColumnName="id", nullable=false, unique=true)}
     * )
     */
    private $responsibilities;

    public function __construct()
    {
        parent::__construct();
        
        $this->isAdministrator = false;
        $this->isModerator = false;
        $this->responsibilities = new ArrayCollection();
    }


    /**
     * Set fromDate
     *
     * @param \DateTime $fromDate
     * @return Membership
     */
    public function setFromDate($fromDate)
    {
        $this->fromDate = $fromDate;

        return $this;
    }

    /**
     * Get fromDate
     *
     * @return \DateTime 
     */
    public function getFromDate()
    {
        return $this->fromDate;
    }

    /**
     * Set toDate
     *
     * @param \DateTime $toDate
     * @return Membership
     */
    public function setToDate($toDate)
    {
        $this->toDate = $toDate;

        return $this;
    }

    /**
     * Get toDate
     *
     * @return \DateTime 
     */
    public function getToDate()
    {
        return $this->toDate;
    }

    /**
     * Set isAdministrator
     *
     * @param boolean $isAdministrator
     * @return Membership
     */
    public function setIsAdministrator($isAdministrator)
    {
        $this->isAdministrator = $isAdministrator;

        return $this;
    }

    /**
     * Get isAdministrator
     *
     * @return boolean 
     */
    public function getIsAdministrator()
    {
        return $this->isAdministrator;
    }

    /**
     * Set isModerator
     *
     * @param boolean $isModerator
     * @return Membership
     */
    public function setIsModerator($isModerator)
    {
        $this->isModerator = $isModerator;

        return $this;
    }

    /**
     * Get isModerator
     *
     * @return boolean 
     */
    public function getIsModerator()
    {
        return $this->isModerator;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Membership
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
     * @return Membership
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
     * Set season
     *
     * @param \Mby\CommunityBundle\Entity\Season $season
     * @return Membership
     */
    public function setSeason(\Mby\CommunityBundle\Entity\Season $season)
    {
        $this->season = $season;

        return $this;
    }

    /**
     * Get season
     *
     * @return \Mby\CommunityBundle\Entity\Season 
     */
    public function getSeason()
    {
        return $this->season;
    }

    /**
     * Add responsibilities
     *
     * @param \Mby\CommunityBundle\Entity\Responsibility $responsibilities
     * @return Membership
     */
    public function addResponsibility(\Mby\CommunityBundle\Entity\Responsibility $responsibilities)
    {
        $this->responsibilities[] = $responsibilities;

        return $this;
    }

    /**
     * Remove responsibilities
     *
     * @param \Mby\CommunityBundle\Entity\Responsibility $responsibilities
     */
    public function removeResponsibility(\Mby\CommunityBundle\Entity\Responsibility $responsibilities)
    {
        $this->responsibilities->removeElement($responsibilities);
    }

    /**
     * Get responsibilities
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getResponsibilities()
    {
        return $this->responsibilities;
    }
}
