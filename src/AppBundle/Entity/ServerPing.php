<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ServerPing
 *
 * @ORM\Table(name="server_ping")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\ServerPingRepository")
 */
class ServerPing
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
     * @var string
     *
     * @ORM\Column(name="url", type="string", length=255, unique=true)
     */
    private $url;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="ping_datetime", type="datetime")
     */
    private $pingDatetime;

    /**
     * @var bool
     *
     * @ORM\Column(name="ping_success", type="boolean")
     */
    private $pingSuccess;

    /**
     * @var int
     *
     * @ORM\Column(name="ping_http_code", type="integer")
     */
    private $pingHttpCode;


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
     * @return ServerPing
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
     * @return ServerPing
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

    /**
     * Set pingDatetime
     *
     * @param \DateTime $pingDatetime
     *
     * @return ServerPing
     */
    public function setPingDatetime($pingDatetime)
    {
        $this->pingDatetime = $pingDatetime;

        return $this;
    }

    /**
     * Get pingDatetime
     *
     * @return \DateTime
     */
    public function getPingDatetime()
    {
        return $this->pingDatetime;
    }

    /**
     * Set pingStatus
     *
     * @param boolean $pingSuccess
     *
     * @return ServerPing
     */
    public function setPingSuccess($pingSuccess)
    {
        $this->pingSuccess = $pingSuccess;

        return $this;
    }

    /**
     * Get pingSucess
     *
     * @return bool
     */
    public function getPingSuccess()
    {
        return $this->pingSuccess;
    }

    /**
     * Set HttpCode
     *
     * @param $pingHttpCode
     *
     * @return ServerPing
     */
    public function setPingHttpCode($pingHttpCode)
    {
        $this->pingHttpCode = $pingHttpCode;
    }

    /**
     * Get HttpCode
     *
     * @return int
     */
    public function getPingHttpCode()
    {
        return $this->pingHttpCode;
    }
}

