<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * HistoryServerPing
 *
 * @ORM\Table(name="history_server_ping")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HistoryServerPingRepository")
 */
class HistoryServerPing
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="pingDatetime", type="datetime")
     */
    private $pingDatetime;

    /**
     * @var string
     *
     * @ORM\Column(name="pingHttpCode", type="string", length=255)
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
     * @return HistoryServerPing
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
     * Set pingDatetime
     *
     * @param \DateTime $pingDatetime
     *
     * @return HistoryServerPing
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
     * Set pingHttpCode
     *
     * @param string $pingHttpCode
     *
     * @return HistoryServerPing
     */
    public function setPingHttpCode($pingHttpCode)
    {
        $this->pingHttpCode = $pingHttpCode;

        return $this;
    }

    /**
     * Get pingHttpCode
     *
     * @return string
     */
    public function getPingHttpCode()
    {
        return $this->pingHttpCode;
    }
}

