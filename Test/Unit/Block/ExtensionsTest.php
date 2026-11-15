<?php

/**
 * @author Mygento Team
 * @copyright 2014-2026 Mygento (https://www.mygento.com)
 * @package Mygento_Base
 */

namespace Mygento\Base\Test\Unit\Block;

use PHPUnit\Framework\TestCase;

class ExtensionsTest extends TestCase
{
    public function testSupportEmailIsAscii(): void
    {
        $source = file_get_contents(dirname(__DIR__, 3) . '/Block/Extensions.php');

        $this->assertSame(1, preg_match('/\$email = \'([^\']+)\';/', $source, $matches));
        $this->assertSame('hello@mygento.com', $matches[1]);
        $this->assertSame(1, preg_match('/^[\x21-\x7e]+$/', $matches[1]));
    }
}
