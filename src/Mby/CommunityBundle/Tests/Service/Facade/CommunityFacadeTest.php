<?php

namespace Mby\CommunityBundle\Tests\Service\Facade;

use Doctrine\Common\Collections\ArrayCollection;
use Mby\CommunityBundle\DependencyInjection\MbyCommunityExtension;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\CommunityPrivilege;
use Mby\CommunityBundle\Entity\Privilege;
use Mby\CommunityBundle\Entity\PrivilegeRepository;
use Mby\CommunityBundle\MbyCommunityBundle;
use Mby\CommunityBundle\Model\PrivilegedUser;
use Mby\CommunityBundle\Service\Facade\CommunityFacade;
use Mby\UserBundle\Entity\User;
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Validator\Mapping\Loader\YamlFileLoader;

class CommunityFacadeTest extends KernelTestCase
{

    /** @var Bundle */
    private $extension;

    /** @var ContainerBuilder */
    private $container;

    protected function setUp()
    {
        static::bootKernel();

        $this->container = static::$kernel->getContainer();

        //$this->loadConfiguration($this->container, 'enabled');

        $this->assertTrue($this->container->has(CommunityFacade::SERVICE_NAME), 'Facade service not defined');
    }

    protected function buildEntities() {
        $community = new Community();
        $community->setId(42);

        $user1 = new User();
        $user1->setId(12);

        $user2 = new User();
        $user2->setId(27);

        $user3 = new User();
        $user3->setId(52);

        $user4 = new User();
        $user4->setId(19);

        $owner = new Privilege();
        $owner->setCode(PrivilegeRepository::OWNER_CODE);

        $admin = new Privilege();
        $admin->setCode(PrivilegeRepository::ADMIN_CODE);

        $moderator = new Privilege();
        $moderator->setCode(PrivilegeRepository::MODERATOR_CODE);

        $privilegedUser1 = new PrivilegedUser();
        $privilegedUser1->setUserId($user1->getId());
        $privilegedUser1->setCommunityId($community->getId());
        $privilegedUser1->setOwner(true);

        $privilegedUser2 = new PrivilegedUser();
        $privilegedUser2->setUserId($user2->getId());
        $privilegedUser2->setCommunityId($community->getId());
        $privilegedUser2->setOwner(true);
        $privilegedUser2->setAdmin(true);

        $privilegedUser3 = new PrivilegedUser();
        $privilegedUser3->setUserId($user3->getId());
        $privilegedUser3->setCommunityId($community->getId());
        $privilegedUser3->setOwner(true);
        $privilegedUser3->setAdmin(true);
        $privilegedUser3->setModerator(true);

        $privilegedUser3b = new PrivilegedUser();
        $privilegedUser3b->setUserId($user3->getId());
        $privilegedUser3b->setCommunityId($community->getId());
        $privilegedUser3b->setAdmin(true);
        $privilegedUser3b->setModerator(true);

        $privilegedUser3c = new PrivilegedUser();
        $privilegedUser3c->setUserId($user3->getId());
        $privilegedUser3c->setCommunityId($community->getId());
        $privilegedUser3c->setAdmin(true);

        $privilegedUser4 = new PrivilegedUser();
        $privilegedUser4->setUserId($user4->getId());
        $privilegedUser4->setCommunityId($community->getId());
        $privilegedUser4->setModerator(true);

        return array(
            'community' => $community,
            'user1' => $user1,
            'user2' => $user2,
            'user3' => $user3,
            'user4' => $user4,
            'privOwner' => $owner,
            'privAdmin' => $admin,
            'privModerator' => $moderator,
            'cp1' => CommunityPrivilege::build($user1, $community, $owner),
            'cp2' => CommunityPrivilege::build($user2, $community, $owner),
            'cp3' => CommunityPrivilege::build($user2, $community, $admin),
            'cp4' => CommunityPrivilege::build($user3, $community, $owner),
            'cp5' => CommunityPrivilege::build($user3, $community, $admin),
            'cp6' => CommunityPrivilege::build($user3, $community, $moderator),
            'cp7' => CommunityPrivilege::build($user4, $community, $moderator),
            'pu1' => $privilegedUser1,
            'pu2' => $privilegedUser2,
            'pu3' => $privilegedUser3,
            'pu3b' => $privilegedUser3b,
            'pu3c' => $privilegedUser3c,
            'pu4' => $privilegedUser4,
        );
    }

    public function testLoadPrivilegedUsers() {
        /** @var CommunityFacade $facade */
        $facade = $this->container->get(CommunityFacade::SERVICE_NAME);

        $entities = $this->buildEntities();
        $community = $entities['community'];
        $user1 = $entities['user1'];
        $user2 = $entities['user2'];
        $user3 = $entities['user3'];
        $user4 = $entities['user4'];
        $owner = $entities['privOwner'];
        $admin = $entities['privAdmin'];
        $moderator = $entities['privModerator'];

        $communityPrivileges = array();
        $communityPrivileges[] = $entities['cp1'];
        $communityPrivileges[] = $entities['cp2'];
        $communityPrivileges[] = $entities['cp3'];
        $communityPrivileges[] = $entities['cp4'];
        $communityPrivileges[] = $entities['cp5'];
        $communityPrivileges[] = $entities['cp6'];
        $communityPrivileges[] = $entities['cp7'];

        $privilegedUsers = $facade->loadPrivilegedUsers($community, $communityPrivileges);

        $this->assertEquals(4, count($privilegedUsers), 'Bad privilegedUsers count');

        $privilegedUser1 = array_shift($privilegedUsers);
        $this->assertTrue($privilegedUser1->isOwner(), 'User1 should be owner');
        $this->assertFalse($privilegedUser1->isAdmin(), 'User1 shouldn\'t be admin');
        $this->assertFalse($privilegedUser1->isModerator(), 'User1 shouldn\'t be moderator');

        $privilegedUser2 = array_shift($privilegedUsers);
        $this->assertTrue($privilegedUser2->isOwner(), 'User2 should be owner');
        $this->assertTrue($privilegedUser2->isAdmin(), 'User2 should be admin');
        $this->assertFalse($privilegedUser2->isModerator(), 'User2 shouldn\'t be moderator');

        $privilegedUser3 = array_shift($privilegedUsers);
        $this->assertTrue($privilegedUser3->isOwner(), 'User3 should be owner');
        $this->assertTrue($privilegedUser3->isAdmin(), 'User3 should be admin');
        $this->assertTrue($privilegedUser3->isModerator(), 'User3 should be moderator');

        $privilegedUser4 = array_shift($privilegedUsers);
        $this->assertFalse($privilegedUser4->isOwner(), 'User4 shouldn\'t be owner');
        $this->assertFalse($privilegedUser4->isAdmin(), 'User4 shouldn\'t be admin');
        $this->assertTrue($privilegedUser4->isModerator(), 'User4 should be moderator');
    }

    /**
     * Test adding CommunityPrivilege
     */
    public function testComparePrivilegedUsersAndCommunityPrivileges1() {
        /** @var CommunityFacade $facade */
        $facade = $this->container->get(CommunityFacade::SERVICE_NAME);

        $entities = $this->buildEntities();
        $community = $entities['community'];
        $community->addPrivilege($entities['cp2']);

        $privilegedUsers = array();
        $privilegedUsers[] = $entities['pu2'];
        $privilegedUsers[] = $entities['pu1'];
        $privilegedUsers[] = $entities['pu3'];

        $comparisons = $facade->comparePrivilegedUsersAndCommunityPrivileges($community, $privilegedUsers);

        $this->assertEquals(1, count($comparisons['keep']), 'Bad CommunityPrivilege to keep count');
        $this->assertEquals(5, count($comparisons['add']), 'Bad CommunityPrivilege to add count');
        $this->assertEquals(0, count($comparisons['remove']), 'Bad CommunityPrivilege to remove count');

        $toKeep = array_map(function($e) {return serialize($e);}, $comparisons['keep']);
        $toAdd =  array_map(function($e) {return serialize($e);}, $comparisons['add']);

        $this->assertContains(serialize($entities['cp2']), $toKeep, 'CommunityPrivilege cp2 should be kept');

        $this->assertContains(serialize($entities['cp1']), $toAdd, 'CommunityPrivilege cp1 should be added');
        $this->assertContains(serialize($entities['cp3']), $toAdd, 'CommunityPrivilege cp1 should be added');
        $this->assertContains(serialize($entities['cp4']), $toAdd, 'CommunityPrivilege cp1 should be added');
        $this->assertContains(serialize($entities['cp5']), $toAdd, 'CommunityPrivilege cp1 should be added');
        $this->assertContains(serialize($entities['cp6']), $toAdd, 'CommunityPrivilege cp1 should be added');
    }

    /**
     * Test remove CommunityPrivilege
     */
    public function testComparePrivilegedUsersAndCommunityPrivileges2() {
        /** @var CommunityFacade $facade */
        $facade = $this->container->get(CommunityFacade::SERVICE_NAME);

        $entities = $this->buildEntities();
        $community = $entities['community'];
        $community
            ->addPrivilege($entities['cp6'])
            ->addPrivilege($entities['cp7'])
            ->addPrivilege($entities['cp1'])
            ->addPrivilege($entities['cp4'])
            ->addPrivilege($entities['cp5'])
        ;

        $privilegedUsers = array();
        $privilegedUsers[] = $entities['pu1'];
        $privilegedUsers[] = $entities['pu3b'];

        $comparisons = $facade->comparePrivilegedUsersAndCommunityPrivileges($community, $privilegedUsers);

        $this->assertEquals(3, count($comparisons['keep']), 'Bad CommunityPrivilege to keep count');
        $this->assertEquals(0, count($comparisons['add']), 'Bad CommunityPrivilege to add count');
        $this->assertEquals(2, count($comparisons['remove']), 'Bad CommunityPrivilege to remove count');

        $toKeep = array_map(function($e) {return serialize($e);}, $comparisons['keep']);
        $toRemove =  array_map(function($e) {return serialize($e);}, $comparisons['remove']);

        $this->assertContains(serialize($entities['cp1']), $toKeep, 'CommunityPrivilege cp1 should be kept');
        $this->assertContains(serialize($entities['cp5']), $toKeep, 'CommunityPrivilege cp5 should be kept');
        $this->assertContains(serialize($entities['cp6']), $toKeep, 'CommunityPrivilege cp6 should be kept');

        $this->assertContains(serialize($entities['cp4']), $toRemove, 'CommunityPrivilege cp4 should be removed');
        $this->assertContains(serialize($entities['cp7']), $toRemove, 'CommunityPrivilege cp7 should be removed');
    }

    /**
     * Test add and remove CommunityPrivilege
     */
    public function testComparePrivilegedUsersAndCommunityPrivileges3() {
        /** @var CommunityFacade $facade */
        $facade = $this->container->get(CommunityFacade::SERVICE_NAME);

        $entities = $this->buildEntities();
        $community = $entities['community'];
        $community->addPrivilege($entities['cp1'])
            ->addPrivilege($entities['cp2'])
            ->addPrivilege($entities['cp4'])
            ->addPrivilege($entities['cp5'])
            ->addPrivilege($entities['cp6']);

        $privilegedUsers = array();
        $privilegedUsers[] = $entities['pu4'];
        $privilegedUsers[] = $entities['pu3c'];

        $comparisons = $facade->comparePrivilegedUsersAndCommunityPrivileges($community, $privilegedUsers);

        $this->assertEquals(1, count($comparisons['keep']), 'Bad CommunityPrivilege to keep count');
        $this->assertEquals(1, count($comparisons['add']), 'Bad CommunityPrivilege to add count');
        $this->assertEquals(4, count($comparisons['remove']), 'Bad CommunityPrivilege to remove count');

        $toKeep = array_map(function($e) {return serialize($e);}, $comparisons['keep']);
        $toAdd =  array_map(function($e) {return serialize($e);}, $comparisons['add']);
        $toRemove =  array_map(function($e) {return serialize($e);}, $comparisons['remove']);

        $this->assertContains(serialize($entities['cp5']), $toKeep, 'CommunityPrivilege cp5 should be kept');

        $this->assertContains(serialize($entities['cp7']), $toAdd, 'CommunityPrivilege cp7 should be added');

        $this->assertContains(serialize($entities['cp1']), $toRemove, 'CommunityPrivilege cp1 should be removed');
        $this->assertContains(serialize($entities['cp2']), $toRemove, 'CommunityPrivilege cp2 should be removed');
        $this->assertContains(serialize($entities['cp4']), $toRemove, 'CommunityPrivilege cp4 should be removed');
        $this->assertContains(serialize($entities['cp6']), $toRemove, 'CommunityPrivilege cp6 should be removed');

    }

}