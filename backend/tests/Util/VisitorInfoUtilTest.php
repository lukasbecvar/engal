<?php

namespace App\Tests\Util;

use App\Util\VisitorInfoUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class VisitorInfoUtilTest
 * 
 * @covers \App\Util\VisitorInfoUtil
 * 
 * @package App\Tests\Util
 */
class VisitorInfoUtilTest extends TestCase
{
    private VisitorInfoUtil $visitorInfoUtil;

    protected function setUp(): void
    {
        $this->visitorInfoUtil = new VisitorInfoUtil();
        parent::setUp();
    }

    /**
     * @covers \App\Util\VisitorInfoUtil::getIP
     */
    public function testGetIp(): void
    {
        // set testing ip
        $_SERVER['HTTP_CLIENT_IP'] = '192.168.0.1';

        // get ip
        $result = $this->visitorInfoUtil->getIP();

        // check value
        $this->assertEquals('192.168.0.1', $result);
    }
}
