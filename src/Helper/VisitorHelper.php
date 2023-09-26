<?php

namespace App\Helper;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Visitor;

/*
    Visitor helper provides main visitor get, set methods
*/

class VisitorHelper
{
    
    private $entityManager;
    private $errorHelper;
    private $logHelper;
    private $siteHelper;
    private $entityHelper;

    public function __construct(
        EntityManagerInterface $entityManager, 
        ErrorHelper $errorHelper, 
        LogHelper $logHelper, 
        SiteHelper $siteHelper,
        EntityHelper $entityHelper
    ) {
        $this->entityManager = $entityManager;
        $this->errorHelper = $errorHelper;
        $this->logHelper = $logHelper;
        $this->siteHelper = $siteHelper;
        $this->entityHelper = $entityHelper;
    }

    public function insertNew($date, $ipAddress, $browser, $os, $location): void 
    {
        // visitor entity
        $visitorEntity = new Visitor();

        // set visitor values
        $visitorEntity->setVisitedSites(1);
        $visitorEntity->setFirstVisit($date);
        $visitorEntity->setLastVisit($date);
        $visitorEntity->setBrowser($browser);
        $visitorEntity->setOs($os);
        $visitorEntity->setLocation($location);
        $visitorEntity->setIpAddress($ipAddress);
    
        // insert new visitor to database
        $this->entityHelper->insertEntity($visitorEntity);
    }

    public function updateVisitor($date, $ipAddress, $browser, $os): void
    {
        // visitor repository
        $visitorRepository = $this->entityManager->getRepository(Visitor::class)->findOneBy(['ip_address' => $ipAddress]);

        // check if visitor repo found
        if (!$visitorRepository) {
            $this->errorHelper->handleError('unexpected visitor with ip: $ipAddress update error, please check database structure', 500);
        } else {

            // get current visited_sites value from database
            $visitedSites = $visitorRepository->getVisitedSites();

            // update values
            $visitorRepository->setVisitedSites($visitedSites +1);
            $visitorRepository->setLastVisit($date);
            $visitorRepository->setBrowser($browser);
            $visitorRepository->setOs($os);

            // update visitor
            $this->entityManager->flush();
        }
    }
    
    public function getLocation($ipAddress): ?string
    {
        $location = null;

        // check if site running on localhost
        if ($this->siteHelper->isRunningLocalhost()) {
            $country = 'HOST';
            $city = 'Location';
        } else {
 
            try {
                // geoplugin url
                $geoplugin_url = $_ENV['GEOPLUGIN_URL'];

                // geoplugin data
                $geoplugin_data = file_get_contents($geoplugin_url.'/json.gp?ip=$ipAddress');

                // decode data
                $details = json_decode($geoplugin_data);
        
                // get country and site from API data
                $country = $details->geoplugin_countryCode;

                // check if city name defined
                if (!empty(explode('/', $details->geoplugin_timezone)[1])) {
                        
                    // get city name from timezone (explode /)
                    $city = explode('/', $details->geoplugin_timezone)[1];
                } else {
                    $city = null;
                }
            } catch (\Exception $e) {

                // set null if data not getted
                $country = null;
                $city = null;

                // log geolocate error
                $this->logHelper->log('geolocate-error', 'error to geolocate ip: ' . $ipAddress . ', error: ' . $e->getMessage());
            }   
        }

        // empty set to null
        if (empty($country)) {
            $country = null;
        }
        if (empty($city)) {
            $city = null;
        }

        // final return
        if  ($country == null or $city == null) {
            $location = 'Unknown';
        } else {
            $location = $country.'/'.$city;
        }

        return $location;
    }
}
