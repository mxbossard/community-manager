<?php

namespace Mby\CommunityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Community
 *
 * @ORM\Table(name="mby_communities")
 * @ORM\Entity(repositoryClass="Mby\CommunityBundle\Entity\CommunityRepository")
 */
class Community extends AbstractBaseEntity
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
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank(message="community.name.not_blank")
     * @Assert\Length(
     *      min = 3,
     *      max = 100,
     *      minMessage = "community.name.length_min",
     *      maxMessage = "community.name.length_max"
     * )
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=100, nullable=true)
     * @Assert\Email(
     *     message = "community.email.valid",
     *     checkMX = true
     * )
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * @var boolean
     *
     * @ORM\Column(name="joinable", type="boolean")
     */
    private $joinable;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     */
    private $public;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Mby\CommunityBundle\Entity\Season", mappedBy="community")
     */
    private $seasons;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Mby\CommunityBundle\Entity\CommunityPrivilege", mappedBy="community")
     */
    private $privileges;

    public function __construct()
    {
        parent::__construct();

        $this->seasons = new ArrayCollection();
        $this->privileges = new ArrayCollection();
        $this->joinable = true;
        $this->public = true;
    }

    /**
     * {@inheritDoc}
     */
    public function __toString() {
        return $this->name;
    }

    /**
     * @param int $id
     * @return Community
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
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
     * Set name
     *
     * @param string $name
     * @return Community
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
     * Set description
     *
     * @param string $description
     * @return Community
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string 
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return Community
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string 
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set note
     *
     * @param string $note
     * @return Community
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
     * Add seasons
     *
     * @param \Mby\CommunityBundle\Entity\Season $seasons
     * @return Community
     */
    public function addSeason(\Mby\CommunityBundle\Entity\Season $seasons)
    {
        $this->seasons[] = $seasons;

        return $this;
    }

    /**
     * Remove seasons
     *
     * @param \Mby\CommunityBundle\Entity\Season $seasons
     */
    public function removeSeason(\Mby\CommunityBundle\Entity\Season $seasons)
    {
        $this->seasons->removeElement($seasons);
    }

    /**
     * Get seasons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getSeasons()
    {
        return $this->seasons;
    }

    /**
     * Set joinable
     *
     * @param boolean $joinable
     * @return Community
     */
    public function setJoinable($joinable)
    {
        $this->joinable = $joinable;

        return $this;
    }

    /**
     * Get joinable
     *
     * @return boolean 
     */
    public function getJoinable()
    {
        return $this->joinable;
    }

    /**
     * Set public
     *
     * @param boolean $public
     * @return Community
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean 
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Add privileges
     *
     * @param \Mby\CommunityBundle\Entity\CommunityPrivilege $privileges
     * @return Community
     */
    public function addPrivilege(\Mby\CommunityBundle\Entity\CommunityPrivilege $privileges)
    {
        $this->privileges[] = $privileges;

        return $this;
    }

    /**
     * Remove privileges
     *
     * @param \Mby\CommunityBundle\Entity\CommunityPrivilege $privileges
     */
    public function removePrivilege(\Mby\CommunityBundle\Entity\CommunityPrivilege $privileges)
    {
        $this->privileges->removeElement($privileges);
    }

    /**
     * Get privileges
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getPrivileges()
    {
        return $this->privileges;
    }
}
