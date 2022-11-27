<?php

declare(strict_types=1);

namespace PhpMyAdmin\SqlParser\Tests\Tools;

use PhpMyAdmin\SqlParser\Tests\TestCase;
use PhpMyAdmin\SqlParser\Token;
use PhpMyAdmin\SqlParser\Tools\ContextGenerator;

use function file_get_contents;
use function getcwd;

class ContextGeneratorTest extends TestCase
{
    public function testFormatName(): void
    {
        $name = ContextGenerator::formatName('MySql');
        $this->assertEquals('MySQL ', $name);

        $name = ContextGenerator::formatName('MySql80000');
        $this->assertEquals('MySQL 8.0', $name);

        $name = ContextGenerator::formatName('MariaDB100200');
        $this->assertEquals('MariaDB 10.2', $name);

        $name = ContextGenerator::formatName('MariaDB100000');
        $this->assertEquals('MariaDB 10.0', $name);
    }

    public function testSortWords(): void
    {
        $wordsArray = ['41' => [['GEOMETRYCOLLECTION', 'DATE']], '35' => [['SCHEMA', 'REPEAT', 'VALUES']]];
        $sortedArray = ContextGenerator::sortWords($wordsArray);
        $this->assertEquals([
            '41' => ['0' => ['DATE', 'GEOMETRYCOLLECTION']],
            '35' => ['0' => ['REPEAT', 'SCHEMA', 'VALUES']],
        ], $wordsArray);
    }

    public function testReadWords(): void
    {
        $testFiles = [getcwd() . '/Tests/Tools/contexts/testContext.txt'];
        $readWords = ContextGenerator::readWords($testFiles);
        $this->assertEquals([
            Token::TYPE_KEYWORD | Token::FLAG_KEYWORD_RESERVED => [8 => ['RESERVED']],
            Token::TYPE_KEYWORD | Token::FLAG_KEYWORD_FUNCTION => [8 => ['FUNCTION']],
            Token::TYPE_KEYWORD | Token::FLAG_KEYWORD_DATA_TYPE => [8 => ['DATATYPE']],
            Token::TYPE_KEYWORD | Token::FLAG_KEYWORD_KEY => [7 => ['KEYWORD']],
            Token::TYPE_KEYWORD => [7 => ['NO_FLAG']],
            Token::TYPE_KEYWORD | Token::FLAG_KEYWORD_RESERVED | 4 => [16 => ['COMPOSED KEYWORD']],
        ], $readWords);
    }

    public function testGenerate(): void
    {
        $testFiles = [getcwd() . '/Tests/Tools/contexts/testContext.txt'];
        $readWords = ContextGenerator::readWords($testFiles);
        ContextGenerator::printWords($readWords);
        $options = [
            'keywords' => $readWords,
            'name' => 'MYSQL TEST',
            'class' => 'ContextTest',
            'link' => 'https://www.phpmyadmin.net/contribute',
        ];
        $generatedTemplate = ContextGenerator::generate($options);
        $expectedTemplate = file_get_contents(getcwd() . '/Tests/Tools/templates/ContextTest.php');
        $this->assertEquals($expectedTemplate, $generatedTemplate);
    }
}
