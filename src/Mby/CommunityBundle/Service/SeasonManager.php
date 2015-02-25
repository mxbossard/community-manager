<?php

namespace Mby\CommunityBundle\Service;

use Doctrine\ORM\EntityManager;

use Mby\UserBundle\Entity\User;
use Mby\CommunityBundle\Entity\Season;
use Mby\CommunityBundle\Entity\Community;
use Mby\CommunityBundle\Entity\SeasonRepository;

class SeasonManager
{

    const SERVICE_NAME = 'season_manager';

    /**
     * @var EntityManager 
     */
    protected $em;

    /**
     * @var PrivilegeManager
     */
    protected $privilegeManager;

    public function __construct(EntityManager $entityManager, PrivilegeManager $privilegeManager)
    {
        $this->em = $entityManager;
        $this->privilegeManager = $privilegeManager;
    }

    /**
     * Create a new season.
     *
     * @param Community $community
     * @param Season $season
     * @throws \Exception
     */
    public function create(Community $community, Season $season)
    {
        $this->assertSeasonDates($community, $season);

        $seasonRepo = $this->em->getRepository('MbyCommunityBundle:Season');
        $lastSeason = $seasonRepo->findLastSeason($community);

        // A season must start after the end date of a previous season.
        // The previous season must be closed : have an end date.
        if ($lastSeason !== null) {
            if ($lastSeason->getToDate() === null) {
                throw new \Exception("previous season must be closed");
            }

            $this->assertDate1StrictlyBeforeDate2($lastSeason->getToDate(), $season->getFromDate(),
                "opening date before previous season closing date");
        }

        $season->setCommunity($community);

        $this->em->persist($season);
    }

    /**
     * Update a season.
     *
     * @param Season $season
     * @throws \Exception
     */
    public function update(Season $season)
    {
        $community = $season->getCommunity();

        $this->assertSeasonDates($community, $season);

        // A season must start after the end date of a previous season.
        // The previous season must be closed : have an end date.
        $seasonRepo = $this->em->getRepository('MbyCommunityBundle:Season');
        $previousSeason = $seasonRepo->findPreviousSeason($season);
        if ($previousSeason !== null) {
            if ($previousSeason->getToDate() === null) {
                throw new \Exception("previous season must be closed");
            }

            $this->assertDate1StrictlyBeforeDate2($previousSeason->getToDate(), $season->getFromDate(),
                "opening date before previous season closing date");
        }

        $followingSeason = $seasonRepo->findFollowingSeason($season);
        if ($followingSeason !== null) {
            $this->assertDate1StrictlyBeforeDate2($season->getToDate(), $followingSeason->getFromDate(),
                "closing date after following season opening date");
        }

        $this->em->persist($season);
    }

    /**
     * Close a season.
     *
     * @param Season $season
     * @param \DateTime $endDate
     * @throws \Exception
     */
    public function close(Season $season, \DateTime $endDate)
    {
        // Merge the season to erase all possible modifications on season entity.
        $mergedSeason = $this->em->merge($season);

        if ($mergedSeason->getToDate() !== null) {
            throw new \Exception("season already closed");
        }

        $this->assertDate1StrictlyBeforeDate2($mergedSeason->getFromDate(), $endDate,
            "closing date not after opening date");

        $mergedSeason->setToDate($endDate);

        $this->em->persist($mergedSeason);
    }

    /**
     * Assert date1 is strictly before date2.
     *
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @param string $message
     * @throws \Exception
     */
    public function assertDate1StrictlyBeforeDate2(\DateTime $date1, \DateTime $date2, $message = "date1 not before date2")
    {
        if ($date1->diff($date2)->d <= 0) {
            throw new \Exception($message);
        }
    }

    /**
     * Assert date1 is before or same day date2.
     *
     * @param \DateTime $date1
     * @param \DateTime $date2
     * @param string $message
     * @throws \Exception
     */
    public function assertDate1NotAfterDate2(\DateTime $date1, \DateTime $date2, $message = "date1 not before date2")
    {
        if ($date1->diff($date2)->d < 0) {
            throw new \Exception($message);
        }
    }

    /**
     * Assert the season dates validity.
     *
     * @param Community $community
     * @param Season $season
     * @throws \Exception
     */
    public function assertSeasonDates(Season $season)
    {
        // fromDate mandatory
        if ($season->getFromDate() === null) {
            throw new \Exception("season opening date is mandatory");
        }

        // toDate need to be after fromDate
        if ($season->getToDate() !== null) {
            $this->assertDate1NotAfterDate2($season->getFromDate(), $season->getToDate(),
                "season closing date must take place after the opening date");
        }

    }

}
