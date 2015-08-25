<?php

namespace Qlowd\FtpadmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ftpxferlog
 *
 * @ORM\Table(name="FtpXferLog")
 * @ORM\Entity
 */
class Ftpxferlog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="username", type="text", nullable=false)
     */
    private $username;

    /**
     * @var string
     *
     * @ORM\Column(name="filename", type="text", nullable=false)
     */
    private $filename;

    /**
     * @var integer
     *
     * @ORM\Column(name="filesize", type="bigint", nullable=false)
     */
    private $filesize;

    /**
     * @var string
     *
     * @ORM\Column(name="ip", type="string", length=255, nullable=false)
     */
    private $ip;

    /**
     * @var string
     *
     * @ORM\Column(name="action", type="string", length=255, nullable=false)
     */
    private $action;

    /**
     * @var string
     *
     * @ORM\Column(name="duration", type="string", length=255, nullable=false)
     */
    private $duration;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="logtime", type="datetime", nullable=false)
     */
    private $logtime;

    /**
     * @var boolean
     *
     * @ORM\Column(name="success", type="boolean", nullable=false)
     */
    private $success;

    /**
     * @var boolean
     *
     * @ORM\Column(name="sync_status", type="boolean", nullable=false)
     */
    private $syncStatus;



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
     * Set username
     *
     * @param string $username
     * @return Ftpxferlog
     */
    public function setUsername($username)
    {
        $this->username = $username;

        return $this;
    }

    /**
     * Get username
     *
     * @return string 
     */
    public function getUsername()
    {
        return $this->username;
    }

    /**
     * Set filename
     *
     * @param string $filename
     * @return Ftpxferlog
     */
    public function setFilename($filename)
    {
        $this->filename = $filename;

        return $this;
    }

    /**
     * Get filename
     *
     * @return string 
     */
    public function getFilename()
    {
        return $this->filename;
    }

    /**
     * Set filesize
     *
     * @param integer $filesize
     * @return Ftpxferlog
     */
    public function setFilesize($filesize)
    {
        $this->filesize = $filesize;

        return $this;
    }

    /**
     * Get filesize
     *
     * @return integer 
     */
    public function getFilesize()
    {
        return $this->filesize;
    }

    /**
     * Set ip
     *
     * @param string $ip
     * @return Ftpxferlog
     */
    public function setIp($ip)
    {
        $this->ip = $ip;

        return $this;
    }

    /**
     * Get ip
     *
     * @return string 
     */
    public function getIp()
    {
        return $this->ip;
    }

    /**
     * Set action
     *
     * @param string $action
     * @return Ftpxferlog
     */
    public function setAction($action)
    {
        $this->action = $action;

        return $this;
    }

    /**
     * Get action
     *
     * @return string 
     */
    public function getAction()
    {
        return $this->action;
    }

    /**
     * Set duration
     *
     * @param string $duration
     * @return Ftpxferlog
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;

        return $this;
    }

    /**
     * Get duration
     *
     * @return string 
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * Set logtime
     *
     * @param \DateTime $logtime
     * @return Ftpxferlog
     */
    public function setLogtime($logtime)
    {
        $this->logtime = $logtime;

        return $this;
    }

    /**
     * Get logtime
     *
     * @return \DateTime 
     */
    public function getLogtime()
    {
        return $this->logtime;
    }

    /**
     * Set success
     *
     * @param boolean $success
     * @return Ftpxferlog
     */
    public function setSuccess($success)
    {
        $this->success = $success;

        return $this;
    }

    /**
     * Get success
     *
     * @return boolean 
     */
    public function getSuccess()
    {
        return $this->success;
    }

    /**
     * Set syncStatus
     *
     * @param boolean $syncStatus
     * @return Ftpxferlog
     */
    public function setSyncStatus($syncStatus)
    {
        $this->syncStatus = $syncStatus;

        return $this;
    }

    /**
     * Get syncStatus
     *
     * @return boolean 
     */
    public function getSyncStatus()
    {
        return $this->syncStatus;
    }
}
