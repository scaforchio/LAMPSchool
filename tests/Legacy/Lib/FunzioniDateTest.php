<?php

namespace LampSchool\Tests\Legacy\Lib;

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../../../lib/fundate.php';

class FunzioniDateTest extends TestCase
{
    public function testShouldConvertStringDateFromEngToItFormat()
    {
        $result = data_aaaammgg_italiana("19001225");

        self::assertEquals("25/12/1900", $result);
    }
}
