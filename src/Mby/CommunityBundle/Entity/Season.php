<?php

namespace Mby\CommunityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Season
 *
 * @ORM\Table(name="mby_seasons")
 * @ORM\Entity(repositoryClass="Mby\CommunityBundle\Entity\SeasonRepository")
 */
class Season extends AbstractBaseEntity
{

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fromDate", type="date")
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
     * @ORM\Column(name="active", type="boolean")
     */
    private $active;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=false)
     * @Assert\NotBlank(message="season.name.not_blank")
     * @Assert\Length(
     *      min = 3,
     *      max = 100,
     *      minMessage = "season.name.length_min",
     *      maxMessage = "season.name.length_max"
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var \Mby\CommunityBundle\Entity\Community
     * 
     * @ORM\ManyToOne(targetEntity="Mby\CommunityBundle\Entity\Community", inversedBy="seasons")
     * @ORM\JoinColumn(name="community_id", referencedColumnName="id", nullable=false)
     */
    private $community;

    /**
     * @var \ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Mby\CommunityBundle\Entity\Membership", mappedBy="season")
     * , fetch="EXTRA_LAZY"
     */
    private $memberships;

    public function __construct()
    {
        parent::__construct();

        $this->active = false;
        $this->memberships = new ArrayCollection();
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
     * Set fromDate
     *
     * @param \DateTime $fromDate
     * @return Season
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
     * @return Season
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
     * Set name
     *
     * @param string $name
     * @return Season
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Season
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
     * Set community
     *
     * @param \Mby\CommunityBundle\Entity\Community $community
     * @return Season
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
     * Add memberships
     *
     * @param \Mby\CommunityBundle\Entity\Membership $memberships
     * @return Season
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

    /**
     * Set active
     *
     * @param boolean $active
     * @return Season
     */
    public function setActive($active)
    {
        $this->active = $active;

        return $this;
    }

    /**
     * Get active
     *
     * @return boolean 
     */
    public function getActive()
    {
        return $this->active;
    }

}
