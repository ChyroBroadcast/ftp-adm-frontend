<?php

namespace Qlowd\FtpadmBundle\Entity;

use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;


class UserRepository extends EntityRepository implements UserProviderInterface
{
        public function loadUserByUsername($username)
        {
            // Manage Customers by vhost. Parse host
            $host = $_SERVER['REMOTE_ADDR'];
            if (inet_pton($host))
                $customer = 'qlowd';
            else {
                $customer = array_values(explode($host))[0];
            }

            $user = $this->createQueryBuilder('u')
                ->leftJoin('Qlowd\FtpadmBundle\Entity\Customer', 'c')
                ->where('u.login = :username')
                ->andWhere('c.name = :customer')
                ->setParameter('username', $username)
                ->setParameter('customer', $customer)
                ->getQuery()
                ->getOneOrNullResult();

            if (null === $user) {
                $message = sprintf(
                    'Unable to find an active admin Qlowd\FtpadmBundle\Entity:User object identified by "%s".',
                    $username
                );
                throw new UsernameNotFoundException($message);
            }

            return $user;
        }

        public function refreshUser(UserInterface $user)
        {
            $class = get_class($user);
            if (!$this->supportsClass($class)) {
                throw new UnsupportedUserException(
                    sprintf(
                        'Instances of "%s" are not supported.',
                        $class
                    )
                );
            }

            return $this->find($user->getId());
        }

        public function supportsClass($class)
        {
            return $this->getEntityName() === $class
                || is_subclass_of($class, $this->getEntityName());
        }
}
