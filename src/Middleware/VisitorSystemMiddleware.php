<?php

namespace App\Middleware;

use App\Helper\EntityHelper;
use App\Helper\LogHelper;
use App\Helper\VisitorHelper;
use App\Entity\Visitor;
use App\Util\EscapeUtil;
use App\Util\VisitorInfoUtil;
use Twig\Environment;

/*
    Visitor system provides basic visitors managment
    Functions: insert new, update exist
*/

class VisitorSystemMiddleware
{ 

    private $visitorHelper;
    private $logHelper;
    private $entityHelper;
    private $vistorInfoUtils;
    private $twig;

    public function __construct(
        VisitorHelper $visitorHelper, 
        LogHelper $logHelper, 
        EntityHelper $entityHelper,
        VisitorInfoUtil $vistorInfoUtils,
        Environment $twig
    ) {
        $this->visitorHelper = $visitorHelper;
        $this->logHelper = $logHelper;
        $this->entityHelper = $entityHelper;
        $this->vistorInfoUtils = $vistorInfoUtils;
        $this->twig = $twig;
    }

    public function onKernelRequest(): void
    {
        // get data to insert
        $date = date("d.m.Y H:i:s");
        $os =VisitorInfoUtil::getOS();
        $ipAddress = VisitorInfoUtil::getIP();
        $browser = VisitorInfoUtil::getBrowser();
        $location = $this->visitorHelper->getLocation($ipAddress);

        // escape inputs
        $ipAddress = EscapeUtil::special_chars_strip($ipAddress);
        $browser = EscapeUtil::special_chars_strip($browser);
        $location = EscapeUtil::special_chars_strip($location);

        // visitor entity
        $visitorEntity = new Visitor();

        // get visitor ip address
        $address = VisitorInfoUtil::getIP();

        // check if visitor found in database
        if (!$this->entityHelper->isEntityExist("ip_address", $address, $visitorEntity)) {

            // insert new visitor
            $this->visitorHelper->insertNew($date, $ipAddress, $browser, $os, $location);
        } else {

            // update exist visitor
            $this->visitorHelper->updateVisitor($date, $ipAddress, $browser, $os);
        }
    }
}
