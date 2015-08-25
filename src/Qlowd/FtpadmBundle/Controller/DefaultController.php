<?php

namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller
{
  /**
  * @Route("/")
  */
    public function indexAction()
    {
        return $this->render('QlowdFtpadmBundle:Default:index.html.twig', array());
    }
}

?>
