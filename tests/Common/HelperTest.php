<?php

use Cleantalk\Common\Helper;
use PHPUnit\Framework\TestCase;

class HelperTest extends TestCase
{
    public function setUp()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
    }

    public function testIp__get()
    {
        self::assertEquals('127.0.0.1', Helper::ip__get());
    }
}
