<?php

namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class FtpController extends Controller
{
  /**
  * @Route("/ftp")
  */
    public function indexAction()
    {
        return $this->render('QlowdFtpadmBundle:Ftp:index.html.twig', array());
    }
}

?>
