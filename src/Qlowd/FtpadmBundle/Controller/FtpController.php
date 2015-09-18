<?php
namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
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
		$users = $em->getRepository('QlowdFtpadmBundle:User')->findByCustomer($user->getCustomer(), array('email' => 'ASC'));
		$customer = $em->getRepository('QlowdFtpadmBundle:Customer')->findOneById($user->getCustomer());
		$path_customer = preg_replace('/^(.+?)\\/*$/', '$1', $customer->getPath());
		$users_list = array();

		foreach ($users as $u) {
			$ftpuser = $em->getRepository('QlowdFtpadmBundle:Ftpuser')->findOneById($u->getId());
			$path = substr($ftpuser->getHomedirectory(), strlen($path_customer));
			$ftpuser->setHomedirectory($path);

			if (isset($ftpuser))
				$users_list[] = array('user' => $u, 'ftpuser' => $ftpuser);
		}

		return $this->render('QlowdFtpadmBundle:Ftp:list.html.twig', array('user' => $users_list));
	}

	/**
	 * @Route("/ftp/add", defaults={"email" = null})
	 * @Route("/ftp/edit/{email}")
	 */
	public function editAction($email = null, Request $request) {
		$em = $this->getDoctrine()->getManager();

		$user = $this->get('security.context')->getToken()->getUser();
		$customer = $em->getRepository('QlowdFtpadmBundle:Customer')->findOneById($user->getCustomer());
		$path_customer = preg_replace('/^(.+?)\\/*$/', '$1', $customer->getPath());

		// edit user
		if (isset($email)) {
			$user = $em->getRepository('QlowdFtpadmBundle:User')->findOneByEmail($email);

			if (!$user)
				return $this->redirect('/ftp');

			$ftpuser = $em->getRepository('QlowdFtpadmBundle:Ftpuser')->findOneById($user->getId());
			$path = substr($ftpuser->getHomedirectory(), strlen($path_customer));
			$ftpuser->setHomedirectory($path);
		}

		// add user
		else {
			$user = new User();
			$ftpuser = new Ftpuser();
		}

		// Initialization of fields missing in the form
		$user->setCustomer($customer);
		$user->setAccess(1);
		$ftpuser->setUid(1004);
		$ftpuser->setGid(1004);
		$ftpuser->setShell('/bin/bash');
		$ftpuser->setLogindate(date_create());
		$ftpuser->setModifdate(date_create());

		$data = array('user' => $user, 'ftpuser' => $ftpuser);
		if ($ftpuser->getAccess() == 'read' or $ftpuser->getAccess() == 'read_write')
			$read = true;
		else
			$read = false;
		if ($ftpuser->getAccess() == 'write' or $ftpuser->getAccess() == 'read_write')
			$write = true;
		else
			$write = false;

		$form = $this->createFormBuilder($data)
			->add('email', 'email', array('label' => 'user.email', 'data' => $user->getEmail()))
			->add('fullname', 'text', array('label' => 'user.fullname', 'data' => $user->getFullname()))
			->add('password1', 'password', array('label' => 'user.password1', 'required' => false))
			->add('password2', 'password', array('label' => 'user.password2', 'required' => false))
			->add('phone', 'text', array('label' => 'user.phone', 'data' => $user->getPhone()))
			->add('isActive', 'checkbox', array('label' => 'user.is_active', 'required' => false, 'data' => $user->getIsActive()))
			->add('isAdmin', 'checkbox', array('label' => 'user.is_admin', 'required' => false, 'data' => $user->getIsAdmin()))
			->add('read_access', 'checkbox', array('label' => 'read', 'required' => false, 'data' => $read))
			->add('write_access', 'checkbox', array('label' => 'write', 'required' => false, 'data' => $write))
			->add('chroot', 'checkbox', array('label' => 'ftpuser.chroot', 'required' => false, 'data' => $ftpuser->getChroot()))
			->add('homedirectory', 'text', array('label' => 'ftpuser.homedirectory', 'data' => $ftpuser->getHomedirectory()))
			->add('save', 'submit')
			->add('reset', 'reset')
			->getForm();

		if ($request->isMethod('POST')) {

			$form->handleRequest($this->getRequest());

			if ($form->isSubmitted() && $form->isValid()) {
				// email
				$email = $form->get('email')->getData();
				if (isset($email) && $email !== '' && $email !== $user->getEmail()) {
					$user->setEmail($email);
				}

				// fullname
				$fullname = $form->get('fullname')->getData();
				$user->setFullname($fullname);

				// password
				$password1 = $form->get('password1')->getData();
				$password2 = $form->get('password2')->getData();
				if ($password1 === $password2 && $password1 !== '')
					$user->setPassword($password1);

				// phone
				$phone = $form->get('phone')->getData();
				$user->setPhone($phone);

				// is active
				$is_active = $form->get('isActive')->getData();
				$user->setIsActive($is_active);

				// is admin
				$is_admin = $form->get('isAdmin')->getData();
				$user->setIsAdmin($is_admin);

				// read / write
				$read = $form->get('read_access')->getData();
				$write = $form->get('write_access')->getData();
				if ($read == 1 && $write == 1)
					$ftpuser->setAccess("read_write");
				elseif ($read == 1)
					$ftpuser->setAccess("read");
				elseif ($write == 1)
					$ftpuser->setAccess("write");
				else
					$ftpuser->setAccess("none");

				// chroot
				$chroot =  $form->get('chroot')->getData();
				$ftpuser->setChroot($chroot);

				// home directory
				$ftpuser->setHomedirectory($path_customer . $form->get('homedirectory')->getData());

				$em->persist($user);
				$em->persist($ftpuser);
				$em->flush();

				return $this->redirect($this->generateUrl('qlowd_ftpadm_ftp'));
			}
		}

		return $this->container->get('templating')->renderResponse('QlowdFtpadmBundle:Ftp:add.html.twig', array('user' => $user, 'ftpuser' => $ftpuser, 'form' => $form->createView()));
	}

	/**
	* @Route("/ftp/del/{email}")
	*/
	public function delAction($email = null) {
		$em = $this->getDoctrine()->getManager();

		// delete user
		if (isset($email)) {
			$user = $em->getRepository('QlowdFtpadmBundle:User')->findOneByEmail($email);

			if (!$user)
				return $this->redirect('/ftp');

			$ftpuser = $em->getRepository('QlowdFtpadmBundle:Ftpuser')->findOneById($user->getId());

			$em->remove($user);
			$em->remove($ftpuser);
			$em->flush();
		}

		return new RedirectResponse($this->container->get('router')->generate('qlowd_ftpadm_ftp'));
	}
}

?>
