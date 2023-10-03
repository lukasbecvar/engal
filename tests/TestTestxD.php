<?php

namespace Simplex\Tests;

use PHPUnit\Framework\TestCase;

class TestTestxD extends TestCase
{
    public function testNotFoundHandling(): void
    {
        $this->assertEquals(404, 404);
    }
}