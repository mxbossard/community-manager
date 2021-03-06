<?php

namespace Mby\CommunityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Privilege
 *
 * @ORM\Table(name="mby_privileges")
 * @ORM\Entity(repositoryClass="Mby\CommunityBundle\Entity\PrivilegeRepository")
 */
class Privilege
{

    /**
     * @var string
     *
     * @ORM\Id
     * @ORM\Column(name="code", type="string", length=16)
     */
    private $code;

    /**
     * @var integer
     *
     * @ORM\Column(name="rank", type="integer", unique=true)
     */
    private $rank;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=100, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    /**
     * {@inheritdoc}
     */
    function __toString()
    {
        return $this->code;
    }

    /**
     * Set code
     *
     * @param string $code
     * @return Privilege
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Get code
     *
     * @return string 
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Privilege
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
     * @return Privilege
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
     * Set rank
     *
     * @param integer $rank
     * @return Privilege
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

        return $this;
    }

    /**
     * Get rank
     *
     * @return integer 
     */
    public function getRank()
    {
        return $this->rank;
    }
}
