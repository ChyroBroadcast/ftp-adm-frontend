<?php
namespace Qlowd\FtpadmBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
//use Qlowd\FtpadmBundle\Entity as Security; // Activate to generate password

class SecurityController extends Controller
{
      /**
      * @Route("/login", name="login_route")
      */
     public function loginAction(Request $request)
     {
            $authenticationUtils = $this->get('security.authentication_utils');


            // get the login error if there is one
            $error = $authenticationUtils->getLastAuthenticationError();

            // last username entered by the user
            $lastUsername = $authenticationUtils->getLastUsername();

            $encoded = '';
             // Used to generate first password :)
            /*$user = new Security\User();
            $plainPassword = 'qlowd';
            $encoder = $this->container->get('security.password_encoder');
            $encoded = $encoder->encodePassword($user, $plainPassword); */

            return $this->render(
                'QlowdFtpadmBundle:Security:login.html.twig',
                array(
                    // last username entered by the user
                    'last_username' => $lastUsername,
                    'error'         => $error,
                    'pass'         => $encoded
                )
            );
     }

     /**
      * @Route("/login_check", name="login_check")
      */
     public function loginCheckAction()
     {
         // this controller will not be executed,
         // as the route is handled by the Security system
     }
}





 ?>
