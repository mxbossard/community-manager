<?php

namespace Mby\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

use Mby\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements FixtureInterface, ContainerAwareInterface
{

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * {@inheritDoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $encoderFactory = $this->container->get('security.encoder_factory');

        $admin = new User();
        $admin->setUsername('admin');
        $admin->setPlainPassword('test');
        $admin->setEmail('admin@test.fr');
        $admin->setEnabled(true);
        $manager->persist($admin);

        $userA = new User();
        $userA->setUsername('userA');
        $userA->setPlainPassword('test');
        $userA->setEmail('usera@test.fr');
        $userA->setEnabled(true);
        $manager->persist($userA);

        $userB = new User();
        $userB->setUsername('userB');
        $userB->setPlainPassword('test');
        $userB->setEmail('userb@test.fr');
        $userB->setEnabled(true);
        $manager->persist($userB);

        $userC = new User();
        $userC->setUsername('userC');
        $userC->setPlainPassword('test');
        $userC->setEmail('userc@test.fr');
        $userC->setEnabled(true);
        $manager->persist($userC);

        $manager->flush();

        $this->addReference('user-admin', $admin);
        $this->addReference('user-userA', $userA);
        $this->addReference('user-userB', $userB);
        $this->addReference('user-userC', $userC);

    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1; // the order in which fixtures will be loaded
    }

}