<?php

declare(strict_types=1);

namespace PhpMyAdmin\SqlParser\Tests\Components;

use PhpMyAdmin\SqlParser\Components\RenameOperation;
use PhpMyAdmin\SqlParser\Parser;
use PhpMyAdmin\SqlParser\Tests\TestCase;

class RenameOperationTest extends TestCase
{
    public function testBuildAll(): void
    {
        $component = RenameOperation::parse(new Parser(), $this->getTokensList('a TO b, c TO d'));
        $this->assertEquals(RenameOperation::buildAll($component), 'a TO b, c TO d');
    }
}
