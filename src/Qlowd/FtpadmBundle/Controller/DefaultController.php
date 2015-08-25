<?php

namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
  /**
  * @Route("/")
  */
    public function indexAction($name)
    {
        return $this->render('QlowdFtpadmBundle:Default:index.html.twig', array('name' => $name));
    }
}
