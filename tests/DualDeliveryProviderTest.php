<?php

declare(strict_types=1);

namespace Dbp\Relay\DispatchBundle\Tests;

use Dbp\Relay\DispatchBundle\DualDeliveryProvider\Vendo\Vendo;
use PHPUnit\Framework\TestCase;

class DualDeliveryProviderTest extends TestCase
{
    public function testVendo()
    {
        $this->assertFalse(Vendo::isValidGZForSubmission(''));
        $this->assertFalse(Vendo::isValidGZForSubmission(' '));
        $this->assertFalse(Vendo::isValidGZForSubmission(str_repeat('a', 26)));
        $this->assertTrue(Vendo::isValidGZForSubmission(str_repeat('🥳', 25)));
        $this->assertTrue(Vendo::isValidGZForSubmission(str_repeat('a', 25)));
        $this->assertTrue(Vendo::isValidGZForSubmission('ok'));
    }
}
