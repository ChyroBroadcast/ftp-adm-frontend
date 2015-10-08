<?php
namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Type;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Qlowd\FtpadmBundle\Entity\User;
use Qlowd\FtpadmBundle\Entity\Ftpuser;
use Qlowd\FtpadmBundle\Entity\Customer;
use Qlowd\FtpadmBundle\Form\Type\FtpAccountType;
use Qlowd\FtpadmBundle\Form\Model\FtpAccount;

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
			//$path = substr($ftpuser->getHomedirectory(), strlen($path_customer));
			//$ftpuser->setHomedirectory($path);


			if (isset($ftpuser))
				$users_list[] = array('user' => $u, 'ftpuser' => $ftpuser);
			//else {
			//	$ftpuser->setHomedirectory('toto');
			//}
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
		$user = $em->getRepository('QlowdFtpadmBundle:User')->findOneByEmail($email);

		// edit user
		if ($user) {
			$ftpuser = $em->getRepository('QlowdFtpadmBundle:Ftpuser')->findOneById($user->getId());
			$path = substr($ftpuser->getHomedirectory(), strlen($path_customer));
			$ftpuser->setHomedirectory($path);
			$editUser = true;
		} else {
			$user = new User();
			$ftpuser = new Ftpuser();
			$editUser = false;
		}

		// Initialization of fields missing in the form
		$user->setCustomer($customer);
		$user->setAccess(1);
		$ftpuser->setUid(1004);
		$ftpuser->setGid(1004);
		$ftpuser->setShell('/bin/bash');
		$ftpuser->setLogindate(date_create());
		$ftpuser->setModifdate(date_create());


		$form = $this->createForm(new FtpAccountType());
		if ($editUser) {
			$popArray = array();
			if ($ftpuser->getAccess() == 'read' or $ftpuser->getAccess() == 'read_write')
				$read = true;
			else
				$read = false;
			if ($ftpuser->getAccess() == 'write' or $ftpuser->getAccess() == 'read_write')
				$write = true;
			else
				$write = false;


			$popArray['email'] = $user->getEmail();
			$popArray['fullname'] = $user->getFullname();
			$popArray['phone'] = $user->getPhone();
			$popArray['isActive'] = $user->getIsActive();
			$popArray['isAdmin'] = $user->getIsAdmin();
			$popArray['read_access'] = $read;
			$popArray['write_access'] = $write;
			$popArray['chroot'] = $ftpuser->getChroot();
			$popArray['homedirectory'] = $ftpuser->getHomedirectory();

			$form->setData($popArray);
		}

		if ($request->isMethod('POST')) {

			$form->handleRequest($this->getRequest());

			if ($form->isSubmitted() && $form->isValid()) {
				// email
				$email = $form->get('email')->getData();
				if (!$editUser)
					$user->setEmail($email);

				// fullname
				$fullname = $form->get('fullname')->getData();
				$user->setFullname($fullname);

				// password
				$password = $form->get('password')->getData();
				if ($password !== '')
					$user->setPassword($password);

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
			} //else {
				/*print_r($popArray).PHP_EOL;
				print $email.PHP_EOL;
				print 'blah'; die();*/
			//}
		}

		return $this->container->get('templating')->renderResponse('QlowdFtpadmBundle:Ftp:add.html.twig', array('user' => $user, 'form' => $form->createView()));
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


	public static function loadValidatorMetadata(ClassMetadata $metadata) {
		$metadata->addPropertyConstraint('email', new NotBlank());
	}
}

?>
