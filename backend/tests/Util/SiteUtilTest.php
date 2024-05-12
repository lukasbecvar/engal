<?php

namespace App\Tests\Util;

use App\Util\SiteUtil;
use PHPUnit\Framework\TestCase;

/**
 * Class SiteUtilTest
 *
 * @covers \App\Util\SiteUtil
 *
 * @package App\Tests\Util
 */
class SiteUtilTest extends TestCase
{
    private SiteUtil $siteUtil;

    protected function setUp(): void
    {
        $this->siteUtil = new SiteUtil();
        parent::setUp();
    }

    /**
     * @covers \App\Util\SiteUtil::isMaintenance
     */
    public function testIsMaintenance(): void
    {
        // mock $_ENV['MAINTENANCE_MODE']
        $_ENV['MAINTENANCE_MODE'] = 'true';

        // act
        $result = $this->siteUtil->isMaintenance();

        // assert
        $this->assertTrue($result);
    }

    /**
     * @covers \App\Util\SiteUtil::isSsl
     */
    public function testIsSsl(): void
    {
        // mock $_SERVER['HTTPS']
        $_SERVER['HTTPS'] = 'on';

        // act
        $result = $this->siteUtil->isSsl();

        // assert
        $this->assertTrue($result);
    }
}
