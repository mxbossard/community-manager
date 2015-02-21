<?php

namespace Mby\CommunityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Mby\UserBundle\Entity\User;


/**
 * CommunityPrivilege
 *
 * @ORM\Table(name="mby_communities_privileges")
 * @ORM\Entity(repositoryClass="Mby\CommunityBundle\Entity\CommunityPrivilegeRepository")
 */
class CommunityPrivilege implements \Serializable
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

    public static function build(User $user, Community $community, Privilege $privilege) {
        $communityPrivilege = new CommunityPrivilege();
        $communityPrivilege->setCommunity($community);
        $communityPrivilege->setUser($user);
        $communityPrivilege->setPrivilege($privilege);

        return $communityPrivilege;
    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * String representation of object
     * @link http://php.net/manual/en/serializable.serialize.php
     * @return string the string representation of the object or null
     */
    public function serialize()
    {
        return serialize(array(
                'userId' => $this->user->getId(),
                'communityId' => $this->community->getId(),
                'privilegeCode' => $this->privilege->getCode(),
            )
        );

    }

    /**
     * (PHP 5 &gt;= 5.1.0)<br/>
     * Constructs the object
     * @link http://php.net/manual/en/serializable.unserialize.php
     * @param string $serialized <p>
     * The string representation of the object.
     * </p>
     * @return void
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);

        //TODO load references with id ?
    }

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
