<?php

namespace Qlowd\FtpadmBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Ftpuser
 *
 * @ORM\Table(name="ftpuser")
 * @ORM\Entity
 */
class Ftpuser
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
     * @var integer
     *
     * @ORM\Column(name="uid", type="integer", nullable=false)
     */
    private $uid;

    /**
     * @var integer
     *
     * @ORM\Column(name="gid", type="integer", nullable=false)
     */
    private $gid;

    /**
     * @var string
     *
     * @ORM\Column(name="shell", type="string", length=255, nullable=false)
     */
    private $shell;

    /**
     * @var string
     *
     * @ORM\Column(name="access", type="string", nullable=false)
     */
    private $access;

    /**
     * @var boolean
     *
     * @ORM\Column(name="chroot", type="boolean", nullable=false)
     */
    private $chroot;

    /**
     * @var string
     *
     * @ORM\Column(name="homedirectory", type="text", nullable=false)
     */
    private $homedirectory;

    /**
     * Set uid
     *
     * @param integer $uid
     * @return Ftpuser
     */
    public function setUid($uid)
    {
        $this->uid = $uid;

        return $this;
    }

    /**
     * Get uid
     *
     * @return integer
     */
    public function getUid()
    {
        return $this->uid;
    }

    /**
     * Set gid
     *
     * @param integer $gid
     * @return Ftpuser
     */
    public function setGid($gid)
    {
        $this->gid = $gid;

        return $this;
    }

    /**
     * Get gid
     *
     * @return integer
     */
    public function getGid()
    {
        return $this->gid;
    }

    /**
     * Set shell
     *
     * @param string $shell
     * @return Ftpuser
     */
    public function setShell($shell)
    {
        $this->shell = $shell;

        return $this;
    }

    /**
     * Get shell
     *
     * @return string
     */
    public function getShell()
    {
        return $this->shell;
    }

    /**
     * Set access
     *
     * @param string $access
     * @return Ftpuser
     */
    public function setAccess($access)
    {
        $this->access = $access;

        return $this;
    }

    /**
     * Get access
     *
     * @return string
     */
    public function getAccess()
    {
        return $this->access;
    }

    /**
     * Set chroot
     *
     * @param boolean $chroot
     * @return Ftpuser
     */
    public function setChroot($chroot)
    {
        $this->chroot = $chroot;

        return $this;
    }

    /**
     * Get chroot
     *
     * @return boolean
     */
    public function getChroot()
    {
        return $this->chroot;
    }

    /**
     * Set homedirectory
     *
     * @param string $homedirectory
     * @return Ftpuser
     */
    public function setHomedirectory($homedirectory)
    {
        $this->homedirectory = $homedirectory;

        return $this;
    }

    /**
     * Get homedirectory
     *
     * @return string
     */
    public function getHomedirectory()
    {
        return $this->homedirectory;
    }

    /**
     * Set id
     *
     * @param \Qlowd\FtpadmBundle\Entity\User $id
     * @return Ftpuser
     */
    public function setId(\Qlowd\FtpadmBundle\Entity\User $id = null)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get id
     *
     * @return \Qlowd\FtpadmBundle\Entity\User
     */
    public function getId()
    {
        return $this->id;
    }
}
