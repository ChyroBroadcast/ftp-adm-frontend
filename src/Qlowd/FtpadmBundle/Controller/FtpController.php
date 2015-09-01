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
		$em = $this->getDoctrine()->getManager();
		$user = $this->get('security.context')->getToken()->getUser();
		$users = $em->getRepository('QlowdFtpadmBundle:User')->findByCustomer($user->getCustomer());

		return $this->render('QlowdFtpadmBundle:Ftp:index.html.twig', array('user' => $users));
	}
}

?>
