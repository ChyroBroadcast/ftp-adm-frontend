<?php
namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Qlowd\FtpadmBundle\Entity\User;
use Qlowd\FtpadmBundle\Entity\Ftpuser;
use Qlowd\FtpadmBundle\Entity\Customer;

class FtpController extends Controller {
	/**
	* @Route("/ftp")
	*/
	public function listAction() {
		$em = $this->getDoctrine()->getManager();
		$user = $this->get('security.context')->getToken()->getUser();
		$users = $em->getRepository('QlowdFtpadmBundle:User')->findByCustomer($user->getCustomer(), array('login' => 'ASC'));
		$customer = $em->getRepository('QlowdFtpadmBundle:Customer')->findOneById($user->getCustomer());
		$path_customer = $customer->getPath();
		$users_list = array();

		foreach ($users as $u) {
			$ftpuser = $em->getRepository('QlowdFtpadmBundle:Ftpuser')->findOneById($u->getId());
			$path = substr($ftpuser->getHomedirectory(), strlen($path_customer)-1);
			$ftpuser->setHomedirectory($path);

			if (isset($ftpuser))
				$users_list[] = array('user' => $u, 'ftpuser' => $ftpuser);
		}

		return $this->render('QlowdFtpadmBundle:Ftp:list.html.twig', array('user' => $users_list));
	}

	/**
	 * @Route("/ftp/add", defaults={"login" = null})
	 * @Route("/ftp/edit/{login}")
	 */
	public function editAction($login = null) {
		$em = $this->getDoctrine()->getManager();

		// edit user
		if (isset($login)) {
			$user = $em->getRepository('QlowdFtpadmBundle:User')->findOneByLogin($login);

			if (!$user)
				return $this->redirect('/ftp');

			$ftpuser = $em->getRepository('QlowdFtpadmBundle:Ftpuser')->findOneById($user->getId());
			$customer = $em->getRepository('QlowdFtpadmBundle:Customer')->findOneById($user->getCustomer());
			$path_customer = $customer->getPath();
			$path = substr($ftpuser->getHomedirectory(), strlen($path_customer)-1);
			$ftpuser->setHomedirectory($path);
		}
		// add user
		else {
			$user = new User();
			$ftpuser = new Ftpuser();
		}

		$request = $this->container->get('request');

		if ($request->getMethod() == 'POST') {
			$form->bindRequest($request);

		if ($form->isValid()) {
			$em->persist($user);
			$em->persist($ftpuser);
			$em->flush();
		}
	}

		return $this->container->get('templating')->renderResponse('QlowdFtpadmBundle:Ftp:add.html.twig', array('user' => $user, 'ftpuser' => $ftpuser));
	}

	/**
	* @Route("/ftp/del/{login}")
	*/
	public function delAction($login = null) {
		$em = $this->getDoctrine()->getManager();
		$user = $em->getRepository('QlowdFtpadmBundle:User')->findOneByLogin($login);

		if (!$user)
			throw new NotFoundHttpException("User not found");

		$em->remove($user);
		$em->flush();

		return new RedirectResponse($this->container->get('router')->generate('qlowd_ftpadm_ftp'));
	}
}

?>
