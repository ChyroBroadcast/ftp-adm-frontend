<?php
namespace Qlowd\FtpadmBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class FtpAccountType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', 'email', array('label' => 'user.email'))
        ->add('fullname', 'text', array('label' => 'user.fullname'))
        ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'The password fields must match.',
                'options' => array('attr' => array('class' => 'password-field')),
                'required' => false,
                'first_options'  => array('label' => 'user.password1'),
                'second_options' => array('label' => 'user.password2'),
            ))
        ->add('phone', 'text', array('label' => 'user.phone'))
        ->add('isActive', 'checkbox', array('label' => 'user.is_active', 'required' => false))
        ->add('isAdmin', 'checkbox', array('label' => 'user.is_admin', 'required' => false))
        ->add('read_access', 'checkbox', array('label' => 'read', 'required' => false))
        ->add('write_access', 'checkbox', array('label' => 'write', 'required' => false))
        ->add('chroot', 'checkbox', array('label' => 'ftpuser.chroot', 'required' => false))
        ->add('homedirectory', 'text', array('label' => 'ftpuser.homedirectory'))
        ->add('save', 'submit')
        ->add('reset', 'reset');
    }

    public function getName()
    {
        return 'registration';
    }
}
?>
