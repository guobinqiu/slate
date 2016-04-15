<?php

namespace Wenwen\AppBundle\Tests\Services;

use VendorIntegration\SSI\PC1\WebService\StatClient;

class SsiConversionReportIteratorTest extends \PHPUnit_Framework_TestCase
{
    private $mockedClient = null;
    public function setUp()
    {
        $this->mockedClient = \Phake::partialMock('\Wenwen\AppBundle\Services\SsiConversionReportIterator');
        $this->mockedClient->initialize(new StatClient('test'), date('Y-m-d'));
    }
    public function testNextConversion()
    {
        \Phake::when($this->mockedClient)->getConversionReport(1)->thenReturn([
            'success' => true,
            'totalNumRows' => 1001,
            'data' => [['row1'], ['row2']],
        ]);
        \Phake::when($this->mockedClient)->getConversionReport(2)->thenReturn([
            'success' => true,
            'totalNumRows' => 1001,
            'data' => [['row1001']],
        ]);
        $this->assertEquals(['row1'], $this->mockedClient->nextConversion(), '1st');
        $this->assertEquals(['row2'], $this->mockedClient->nextConversion(), '2nd');
        $this->assertEquals(['row1001'], $this->mockedClient->nextConversion(), '1001th');
        $this->assertNull($this->mockedClient->nextConversion(), '1002th');
    }
}
