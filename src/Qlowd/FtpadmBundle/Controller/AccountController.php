<?php
namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Qlowd\FtpadmBundle\Form\Type\AccountInfoType;

class AccountController extends Controller
{
  private function ibanIsValid($iban){
    $iban = strtr($iban, array('-' => '', ' ' => ''));
    $iban = substr($iban, 4) . substr($iban, 0, 4);
    $iban = strtr($iban, array(
      'A' => '10',  'B' => '11',
      'C' => '12',  'D' => '13',
      'E' => '14',  'F' => '15',
      'G' => '16',  'H' => '17',
      'I' => '18',  'J' => '19',
      'K' => '20',  'L' => '21',
      'M' => '22',  'N' => '23',
      'O' => '24',  'P' => '25',
      'Q' => '26',  'R' => '27',
      'S' => '28',  'T' => '29',
      'U' => '30',  'V' => '31',
      'W' => '32',  'X' => '33',
      'Y' => '34',  'Z' => '35'
    ));
    $rem = bcmod($iban, '97');
    return $rem === '1';
  }
  
	/**
	* @Route("/account")
	*/
	public function indexAction()
	{
		$em = $this->getDoctrine()->getManager();
		$user = $this->get('security.context')->getToken()->getUser();
		$customer = $em->getRepository('QlowdFtpadmBundle:Customer')->findOneById($user->getCustomer());
    $address = $em->getRepository('QlowdFtpadmBundle:Address')->findOneById(1);
		//print_r($customer); die();
		//print_r($customer->getName()); die();
    
    $form = $this->createForm(new AccountInfoType());
    $popArray = array();
    
    $popArray['street'] = $address->getStreet();
    $popArray['zip_code'] = $address->getZipCode();
    $popArray['city'] = $address->getCity();
    $popArray['country'] = $address->getCountry();
    $popArray['phone'] = $address->getPhone();
    $popArray['iban'] = $address->getIban();
    $popArray['vat_number'] = $address->getVatNumber();
    
    $form->setData($popArray);
    
		return $this->render('QlowdFtpadmBundle:Account:index.html.twig', array(
      'customer' => $customer,
      'address'  => $address,
      'form'     => $form->createView()
    ));
	}
}

?>
