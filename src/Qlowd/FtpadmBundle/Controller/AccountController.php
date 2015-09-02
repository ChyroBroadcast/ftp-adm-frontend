<?php
namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class AccountController extends Controller
{
	/**
	* @Route("/account")
	*/
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$user = $this->get('security.context')->getToken()->getUser();
		$customer = $em->getRepository('QlowdFtpadmBundle:Customer')->findOneById($user->getCustomer());
		//print_r($customer); die();
		//print_r($customer->getName()); die();
		return $this->render('QlowdFtpadmBundle:Account:index.html.twig', array('customer' => $customer));
	}
}

?>
