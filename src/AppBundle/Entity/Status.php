<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Status
 *
 * @ORM\Table(name="status")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\StatusRepository")
 */
class Status
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
     * @var \DateTime
     *
     * @ORM\Column(name="last_success", type="datetime", nullable=true)
     */
    private $lastSuccess;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_error", type="datetime", nullable=true)
     */
    private $lastError;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;


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
     * Set lastSuccess
     *
     * @param \DateTime $lastSuccess
     *
     * @return Status
     */
    public function setLastSuccess($lastSuccess)
    {
        $this->lastSuccess = $lastSuccess;

        return $this;
    }

    /**
     * Get lastSuccess
     *
     * @return \DateTime
     */
    public function getLastSuccess()
    {
        return $this->lastSuccess;
    }

    /**
     * Set lastError
     *
     * @param \DateTime $lastError
     *
     * @return Status
     */
    public function setLastError($lastError)
    {
        $this->lastError = $lastError;

        return $this;
    }

    /**
     * Get lastError
     *
     * @return \DateTime
     */
    public function getLastError()
    {
        return $this->lastError;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Status
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
     * Set url
     *
     * @param string $url
     *
     * @return Status
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }
}

