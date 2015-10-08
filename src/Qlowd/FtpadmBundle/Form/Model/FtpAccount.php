<?php
namespace Qlowd\FtpadmBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Qlowd\FtpadmBundle\Entity\User;
use Qlowd\FtpadmBundle\Entity\Ftpuser;

class FtpAccount {
    /**
    * @Assert\Type(type="Qlowd\FtpadmBundle\Entity\User")
    * @Assert\Valid()
    */
    protected $user;

    /**
    * @Assert\Type(type="Qlowd\FtpadmBundle\Entity\Ftpuser")
    * @Assert\Valid()
    */
    protected $ftpuser;

    public function setUser(User $user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
    public function setFtpuser(Ftpuser $ftpuser)
    {
        $this->ftpuser = $ftpuser;
    }

    public function getFtpuser()
    {
        return $this->ftpuser;
    }

    public function getName()
    {
        return 'ftpaccount';
    }
}

?>
