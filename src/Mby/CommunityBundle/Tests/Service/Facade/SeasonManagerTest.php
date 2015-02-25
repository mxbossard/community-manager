<?php

namespace Mby\CommunityBundle\Tests\Service\Facade;

use Doctrine\Common\Collections\ArrayCollection;
use Mby\CommunityBundle\DependencyInjection\MbyCommunityExtension;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\CommunityPrivilege;
use Mby\CommunityBundle\Entity\Privilege;
use Mby\CommunityBundle\Entity\PrivilegeRepository;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\MbyCommunityBundle;
use Mby\CommunityBundle\Model\PrivilegedUser;
use Mby\CommunityBundle\Service\Facade\CommunityFacade;
use Mby\CommunityBundle\Service\SeasonManager;
use Mby\UserBundle\Entity\User;
use phpDocumentor\Reflection\DocBlock\Type\Collection;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Config\FileLocator;
use Symfony\Component\Validator\Mapping\Loader\YamlFileLoader;

class SeasonManagerTest extends KernelTestCase
{

    /** @var ContainerBuilder */
    private $container;

    protected function setUp()
    {
        static::bootKernel();

        $this->container = static::$kernel->getContainer();

        $this->assertTrue($this->container->has(SeasonManager::SERVICE_NAME), 'service not defined');
    }

    /**
     *
     */
    public function testAssertDate1StrictlyBeforeDate2() {
        /** @var SeasonManager $manager */
        $manager = $this->container->get(SeasonManager::SERVICE_NAME);

        $yesterday = new \DateTime('today - 1 days');
        $today = new \DateTime('today');

        // Check the right way
        try {
            $manager->assertDate1StrictlyBeforeDate2($yesterday, $today);
        } catch (\Exception $e) {
            $this->fail("yesterday not before today");
        }

        // Check the bad way
        try {
            $manager->assertDate1StrictlyBeforeDate2($today, $yesterday);
            $this->fail("today before yesterday");
        } catch (\Exception $e) {

        }

        try {
            $manager->assertDate1StrictlyBeforeDate2($today, $today);
            $this->fail("today before today");
        } catch (\Exception $e) {

        }
    }

    public function testAssertDate1NotAfterDate2() {
        /** @var SeasonManager $manager */
        $manager = $this->container->get(SeasonManager::SERVICE_NAME);

        $yesterday = new \DateTime('today - 1 days');
        $today = new \DateTime('today');

        // Check the right way
        try {
            $manager->assertDate1NotAfterDate2($yesterday, $today);
        } catch (\Exception $e) {
            $this->fail("yesterday not before today");
        }

        // Check the bad way
        try {
            $manager->assertDate1NotAfterDate2($today, $yesterday);
            $this->fail("today before yesterday");
        } catch (\Exception $e) {

        }

        try {
            $manager->assertDate1NotAfterDate2($today, $today);
        } catch (\Exception $e) {
            $this->fail("today before today");
        }
    }

    public function testAssertSeasonDates() {
        /** @var SeasonManager $manager */
        $manager = $this->container->get(SeasonManager::SERVICE_NAME);

        $yesterday = new \DateTime('today - 1 day');
        $today = new \DateTime('today');

        $season = new Season();
        $lastSeason = null;

        // check null from date
        try {
            $manager->assertSeasonDates($season);
            $this->fail("Exception should be thrown because fromDate is null");
        } catch (\Exception $e) {

        }

        // check not null from date
        $season->setFromDate($yesterday);
        try {
            $manager->assertSeasonDates($season);
        } catch (\Exception $e) {
            $this->fail("Exception should not be thrown");
        }

        // check not null to date
        $season->setFromDate($yesterday);
        $season->setToDate($today);
        try {
            $manager->assertSeasonDates($season);
        } catch (\Exception $e) {
            $this->fail("Exception should not be thrown");
        }

        // check not null to date
        $season->setFromDate($today);
        $season->setToDate($today);
        try {
            $manager->assertSeasonDates($season);
        } catch (\Exception $e) {
            $this->fail("Exception should not be thrown");
        }
    }
}