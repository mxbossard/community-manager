<?php

namespace Mby\CommunityBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * Responsibility
 *
 * @ORM\Table(name="mby_responsibilities")
 * @ORM\Entity(repositoryClass="Mby\CommunityBundle\Entity\ResponsibilityRepository")
 */
class Responsibility extends AbstractBaseEntity
{

/**
     * @var string
     *
     * @ORM\Column(name="code", type="string")
     * @ORM\Id
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
     * @ORM\Column(name="name", type="string", length=100)
     * @Assert\NotBlank(message="responsibility.name.not_blank")
     * @Assert\Length(
     *      min = 3,
     *      max = 100,
     *      minMessage = "responsibility.name.length_min",
     *      maxMessage = "responsibility.name.length_max"
     * )     
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="note", type="text", nullable=true)
     */
    private $note;

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    function __toString()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return Responsibility
     */
    public function setCode($code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Responsibility
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return int
     */
    public function getRank()
    {
        return $this->rank;
    }

    /**
     * @param int $rank
     * @return Responsibility
     */
    public function setRank($rank)
    {
        $this->rank = $rank;

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
     * @return Responsibility
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
}
