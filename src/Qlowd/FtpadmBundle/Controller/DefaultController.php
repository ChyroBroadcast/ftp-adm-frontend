<?php

namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DefaultController extends Controller {
	/**
	* @Route("/")
	*/
    public function indexAction() {
		$doc = $this->container->get('doctrine')->getManager();
		/**
		 * TODO: 'qlowd' should be changed with correct customer
		 */
		$user = $this->get('security.context')->getToken()->getUser();
		$customer = $doc->getRepository('QlowdFtpadmBundle:Customer')->findOneById($user->getId());

		$trans = $this->get('translator');

		return $this->render('QlowdFtpadmBundle:Default:index.html.twig', array(
			'customer' => array(
				'id' => $customer->getId(),
				'name' => $customer->getName(),
				'used_space' => intval($customer->getUsedSpace()),
				'used_space_p' => DefaultController::convertSize(intval($customer->getUsedSpace()), $trans),
				'total_space' => intval($customer->getTotalSpace()),
				'total_space_p' => DefaultController::convertSize(intval($customer->getTotalSpace()), $trans),
				'pct_used' => round(100.0 * $customer->getUsedSpace() / $customer->getTotalSpace(), 0),
				'pct_used_p' => round(100.0 * $customer->getUsedSpace() / $customer->getTotalSpace(), 0) . '%'
			)
		));
    }

	public static function convertSize($size, $trans) {
		$mult = 0;
		while ($size > 1024 && $mult < 4) {
			$mult++;
			$size /= 1024;
		}

		$fixed = 0;
		if ($size < 10)
			$fixed = 2;
		else if ($size < 100)
			$fixed = 1;

		switch ($mult) {
			case 0:
				return $size . $trans->trans('util.size.B');
			case 1:
				return round($size, $fixed) . $trans->trans('util.size.KB');
			case 2:
				return round($size, $fixed) . $trans->trans('util.size.MB');
			case 3:
				return round($size, $fixed) . $trans->trans('util.size.GB');

			default:
				return round($size, $fixed) . $trans->trans('util.size.TB');
		}
	}
}

?>
