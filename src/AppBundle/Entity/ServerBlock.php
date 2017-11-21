<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServerBlock
 *
 * @ORM\Table(name="server_block")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServerBlockRepository")
 */
class ServerBlock
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var bool
     *
     * @ORM\Column(name="free", type="boolean")
     */
    private $free;

    /**
     * @var string
     *
     * @ORM\Column(name="user", type="string", length=255, nullable=true)
     */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="user_mail", type="string", length=255, nullable=true)
     */
    private $userMail;

    /**
     * @var string
     *
     * @ORM\Column(name="reason", type="string", length=255, nullable=true)
     */
    private $reason;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="blocked_since", type="datetime", nullable=true)
     */
    private $blockedSince;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return ServerBlock
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
     * Set free
     *
     * @param boolean $free
     *
     * @return ServerBlock
     */
    public function setFree($free)
    {
        $this->free = $free;

        return $this;
    }

    /**
     * Get free
     *
     * @return bool
     */
    public function getFree()
    {
        return $this->free;
    }

    /**
     * Set user
     *
     * @param string $user
     *
     * @return ServerBlock
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set userMail
     *
     * @param string $userMail
     *
     * @return ServerBlock
     */
    public function setUserMail($userMail)
    {
        $this->userMail = $userMail;

        return $this;
    }

    /**
     * Get userMail
     *
     * @return string
     */
    public function getUserMail()
    {
        return $this->userMail;
    }

    /**
     * Set reason
     *
     * @param string $reason
     *
     * @return ServerBlock
     */
    public function setReason($reason)
    {
        $this->reason = $reason;

        return $this;
    }

    /**
     * Get reason
     *
     * @return string
     */
    public function getReason()
    {
        return $this->reason;
    }

    /**
     * Set blockedSince
     *
     * @param \DateTime $blockedSince
     *
     * @return ServerBlock
     */
    public function setBlockedSince($blockedSince)
    {
        $this->blockedSince = $blockedSince;

        return $this;
    }

    /**
     * Get blockedSince
     *
     * @return \DateTime
     */
    public function getBlockedSince()
    {
        return $this->blockedSince;
    }
}

