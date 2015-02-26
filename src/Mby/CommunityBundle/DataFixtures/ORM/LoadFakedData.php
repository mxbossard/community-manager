<?php

namespace Mby\CommunityBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Faker\Factory;
use Faker\ORM\Doctrine\Populator;

use Mby\CommunityBundle\Entity\Membership;
use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\CommunityPrivilege;

class LoadFakedData extends AbstractFixture implements OrderedFixtureInterface
{
    const COUNT_MULTIPLIER = 100;

    const MAX_STEP_MULTIPLIER = 5;

    const FLUSH_STEP = 1000;

    var $_persistCount = 0;
    var $_activityMonitor = array();
    var $_lastActivityMonitored = null;

    var $insertedUsers;
    var $insertedCommunities;
    var $insertedSeasons;

    var $seed = 1;

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1000; // the order in which fixtures will be loaded
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $countMultiplier = LoadFakedData::COUNT_MULTIPLIER;

        $this->loadFakedEntities($manager, $countMultiplier);

        $this->loadFakedRelations($manager);

        /*
        while($countMultiplier > 0) {
            $stepMultiplier = min(LoadFakedData::MAX_STEP_MULTIPLIER, $countMultiplier);

            try {
                echo sprintf("Fake loading step launched with %d multiplier.\n", $stepMultiplier);
                $this->loadFakedEntities($manager, $stepMultiplier);
                $this->loadFakedRelations($manager);
                $countMultiplier = $countMultiplier - $stepMultiplier;
            } catch (UniqueConstraintViolationException $e) {
                echo sprintf("Unique detected with seed %d !\n", $this->seed);
            }

        }
        */

        $manager->flush();
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadFakedEntities(ObjectManager $manager, $countMultiplier)
    {
        $generator = Factory::create();
        $generator->seed($this->seed);

        $populator = new Populator($generator, $manager);
        $populator->addEntity('Mby\UserBundle\Entity\User', 20 * $countMultiplier, array(
            'username' => function () use ($generator) {
                return $generator->unique()->userName();
            },
            'email' => function () use ($generator) {
                return $generator->unique()->email();
            },
            'password' => '7TZG7Uow2b3rR5EfU079E6I6uXW6MQL04jtobQyJ5mhsQdP8YKu3qJJPbW00iZxt8U2HBtsB4hqzmkvZ9nEbRg==',//function() use ($generator) { return '7TZG7Uow2b3rR5EfU079E6I6uXW6MQL04jtobQyJ5mhsQdP8YKu3qJJPbW00iZxt8U2HBtsB4hqzmkvZ9nEbRg=='; },
            'salt' => 'nzq6aewxb3ksg8800g80cgwoccwssg0',//function() use ($generator) { return 'nzq6aewxb3ksg8800g80cgwoccwssg0'; },
            'enabled' => true,
            'locked' => false,
            'expired' => false,
            'expiresAt' => null,
            'credentialsExpired' => false, //function() use ($generator) { return false; },
            'credentialsExpireAt' => null,
        ), array(
            'monitorActivity' => function ($entity) {
                $this->monitorActivity($entity);
            },
        ));

        $populator->addEntity('Mby\CommunityBundle\Entity\Community', 1 * $countMultiplier, array(
            'name' => function () use ($generator) {
                return $generator->company();
            },
            'description' => function () use ($generator) {
                return $generator->text(1000);
            },
            'email' => function () use ($generator) {
                return $generator->unique()->email();
            },
        ), array(
            'monitorActivity' => function ($entity) {
                $this->monitorActivity($entity);
            },
        ));

        $populator->addEntity('Mby\CommunityBundle\Entity\Season', 1 * $countMultiplier, array(
            'name' => function () use ($generator) {
                return $generator->lexify('????? 2014-2015');
            },
            'fromDate' => function () use ($generator) {
                return $generator->dateTimeBetween('2014-09-01', '2014-12-01');
            },
            'toDate' => function () use ($generator) {
                return $generator->optional(null);
            },
            'note' => function () use ($generator) {
                return $generator->text(500);
            },
            'active' => true,
        ), array(
            'monitorActivity' => function ($entity) {
                $this->monitorActivity($entity);
            },
        ));

        $insertedEntities = $populator->execute();

        $populator = new Populator($generator, $manager);
        $populator->addEntity('Mby\CommunityBundle\Entity\Season', 1 * $countMultiplier, array(
            'name' => function () use ($generator) {
                return $generator->lexify('????? 2013-2014');
            },
            'fromDate' => function () use ($generator) {
                return $generator->dateTimeBetween('2013-09-01', '2013-12-01');
            },
            'toDate' => function () use ($generator) {
                return $generator->dateTimeBetween('2014-06-01', '2014-08-01');
            },
            'note' => function () use ($generator) {
                return $generator->text(500);
            },
            'active' => false,
            'community' => function() use($insertedEntities) {
                return $this->fecthRandom($insertedEntities['Mby\CommunityBundle\Entity\Community']);
            },
        ), array(
            'monitorActivity' => function ($entity) {
                $this->monitorActivity($entity);
            },
        ));
        $season2013Entities = $populator->execute();
        $insertedEntities['Mby\CommunityBundle\Entity\Season'] = array_merge($insertedEntities['Mby\CommunityBundle\Entity\Season'], $season2013Entities['Mby\CommunityBundle\Entity\Season']);

        $populator = new Populator($generator, $manager);
        $populator->addEntity('Mby\CommunityBundle\Entity\Season', 1 * $countMultiplier, array(
            'name' => function () use ($generator) {
                return $generator->lexify('????? 2012-2013');
            },
            'fromDate' => function () use ($generator) {
                return $generator->dateTimeBetween('2012-09-01', '2012-12-01');
            },
            'toDate' => function () use ($generator) {
                return $generator->dateTimeBetween('2013-06-01', '2013-08-01');
            },
            'note' => function () use ($generator) {
                return $generator->text(500);
            },
            'active' => false,
            'community' => function() use($insertedEntities) {
                return $this->fecthRandom($insertedEntities['Mby\CommunityBundle\Entity\Community']);
            },
        ), array(
            'monitorActivity' => function ($entity) {
                $this->monitorActivity($entity);
            },
        ));
        $season2012Entities = $populator->execute();
        $insertedEntities['Mby\CommunityBundle\Entity\Season'] = array_merge($insertedEntities['Mby\CommunityBundle\Entity\Season'], $season2012Entities['Mby\CommunityBundle\Entity\Season']);

        $this->insertedUsers = &$insertedEntities['Mby\UserBundle\Entity\User'];
        $this->insertedCommunities = &$insertedEntities['Mby\CommunityBundle\Entity\Community'];
        $this->insertedSeasons = &$insertedEntities['Mby\CommunityBundle\Entity\Season'];
    }

    /**
     * @param ObjectManager $manager
     */
    public function loadFakedRelations(ObjectManager $manager)
    {
        $this->loadRandMemberships($manager);

        $this->loadRandCommunityPrivileges($manager);
    }

    /**
     * @param ObjectManager $manager
     * @param $insertedEntities
     */
    public function loadRandCommunityPrivileges(ObjectManager $manager)
    {
        $ownerPrivilege = $this->getReference('priv-owner');
        $adminPrivilege = $this->getReference('priv-admin');
        $moderatorPrivilege = $this->getReference('priv-moderator');

        $nbUsers = count($this->insertedUsers);

        //error_log(sprintf("[%s] Before loop \n", date('H:i:s')));

        foreach ($this->insertedCommunities as $community) {
            //error_log(sprintf("[%s] looping \n", date('H:i:s')));

            $userChoosed = array();

            $nbOwner = floor(log(rand(3, 100)));
            for ($k = 0; $k < $nbOwner; $k++) {
                $index = $this->randExclude(0, $nbUsers - 1, $userChoosed);
                $userChoosed[$index] = true;
                $user = $this->insertedUsers[$index];

                $this->persist($manager, CommunityPrivilege::build($user, $community, $ownerPrivilege));
            }

            $nbAdmin = floor(log(rand(3, 300)));
            for ($k = 0; $k < $nbAdmin; $k++) {
                $index = $this->randExclude(0, $nbUsers - 1, $userChoosed);
                $userChoosed[$index] = true;
                $user = $this->insertedUsers[$index];

                $this->persist($manager, CommunityPrivilege::build($user, $community, $adminPrivilege));
            }

            $nbModerator = floor(log(rand(3, 1000)));
            for ($k = 0; $k < $nbModerator; $k++) {
                $index = $this->randExclude(0, $nbUsers - 1, $userChoosed);
                $userChoosed[$index] = true;
                $user = $this->insertedUsers[$index];

                $this->persist($manager, CommunityPrivilege::build($user, $community, $moderatorPrivilege));
            }

        }

    }

    /**
     * @param ObjectManager $manager
     * @param $insertedEntities
     */
    public function loadRandMemberships(ObjectManager $manager)
    {
        $responsibilities = array(
            'applicant' => $this->getReference('resp-applicant'),
            'member' => $this->getReference('resp-member'),
            'president' => $this->getReference('resp-president'),
            'vice_president' => $this->getReference('resp-vice_president'),
            'treasurer' => $this->getReference('resp-treasurer'),
            'secretary' => $this->getReference('resp-secretary'),
        );

        $nbUsers = count($this->insertedUsers);

        $today = new \DateTime('today');
        $yesterday = new \DateTime('today -1 day');

        foreach ($this->insertedSeasons as $season) {
            $userChoosed = array();

            $random = rand(0, 999);
            if ($random < 1) {
                $maxMember = min($nbUsers / 10, 10000, $nbUsers);
            } else if ($random < 10) {
                $maxMember = min($nbUsers / 100, 1000, $nbUsers);
            } else if ($random < 100) {
                $maxMember = min($nbUsers / 500, 100, $nbUsers);
            } else if ($random < 600) {
                $maxMember = min(max($nbUsers / 500, 30), $nbUsers);
            } else {
                $maxMember = min(max($nbUsers / 1000, 10), $nbUsers);
            }

            $nbMember = rand(1, $maxMember);

            for ($k = 0; $k < $nbMember; $k++) {
                $index = $this->randExclude(0, $nbUsers - 1, $userChoosed);
                $userChoosed[$index] = true;
                $user = $this->insertedUsers[$index];

                if ($user === null) {
                    break;
                }

                $membership = new Membership();
                $membership->setSeason($season);
                $membership->setUser($user);

                $respRand = rand(0, 9);
                if ($respRand <= 3) {
                    $membership->addResponsibility($responsibilities['applicant']);
                    $membership->setApplicationDate($today);
                } else {
                    $membership->addResponsibility($responsibilities['member']);
                    $membership->setApplicationDate($yesterday);
                    $membership->setFromDate($today);
                    $respRand2 = rand(2, 5);
                    $membership->addResponsibility($responsibilities[array_keys($responsibilities)[$respRand2]]);
                }

                $this->persist($manager, $membership);
            }

        }
    }

    protected function persist(ObjectManager $manager, $entity) {
        $this->monitorActivity($entity);
        $manager->persist($entity);

        $this->_persistCount ++;
        if($this->_persistCount > LoadFakedData::FLUSH_STEP) {
            $manager->flush();
            $this->_persistCount = 0;
        }
    }

    protected function monitorActivity($entity) {
        if ($entity !== null) {
            $className = get_class($entity);

            if ($this->_lastActivityMonitored !== null && $className !== $this->_lastActivityMonitored) {
                echo sprintf("[%s] Finish inserting %d %s entities. \n", date('H:i:s'), $this->_activityMonitor[$this->_lastActivityMonitored], $this->_lastActivityMonitored);
            }

            $this->_lastActivityMonitored = $className;

            if (array_key_exists($className, $this->_activityMonitor)) {
                $this->_activityMonitor[$className] ++;

                if ($this->_activityMonitor[$className] % (LoadFakedData::FLUSH_STEP) === 0) {
                    echo sprintf("[%s] Inserted %d %s entities ... \n", date('H:i:s'), $this->_activityMonitor[$className], $className);
                }
            } else {
                $this->_activityMonitor[$className] = 1;
            }

            //echo $this->_activityMonitor[$class];
        }
    }

    protected function fecthRandom($entitiesArray) {
        $entitiy = $entitiesArray[rand(0, count($entitiesArray) -1)];
        //var_dump($entitiy);
        return $entitiy;
    }

    protected function randExclude($min, $max, $excludedKeys = null) {
        if (! is_array($excludedKeys)) {
            $n = rand($min, $max);
        } else {
            if ($max - $min < count($excludedKeys) * 2) {
                throw new \Exception("too long to generate a randExclude number");
            }

            do {
                $n = rand($min, $max);
            } while(isset($excludedKeys[$n]));
        }

        return $n;
    }

}