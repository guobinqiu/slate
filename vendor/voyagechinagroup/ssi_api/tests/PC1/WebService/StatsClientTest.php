<?php
namespace VendorIntegration\SSI\PC1\WebRequest;

use VendorIntegration\SSI\PC1\WebService\StatClient;

class StatClientTest extends \PHPUnit_Framework_TestCase
{
    private $client = null;

    public function setUp()
    {
        $this->client = new StatClient('abc');
    }

    public function testCreateConversionReportRequest()
    {
        $req = $this->client->createConversionReportRequest(date('Y-m-d'));

        $uri = new \Net_URL2($req->uri);
        $query = $uri->getQueryVariables();
        $this->assertEquals('GET', $req->method);
        $this->assertEquals('/stats/lead_report.json', $uri->getPath());
        $this->assertEquals(
            [
            'api_key' => 'abc',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'page' => '1',
            'limit' => '1000',
            'filter' => [
            'Stat.offer_id' => 3135,
            ],
            ], $query
        );
    }
    public function testCreateConversionReportRequestWithPage()
    {
        $req = $this->client->createConversionReportRequest(date('Y-m-d'), 2);

        $uri = new \Net_URL2($req->uri);
        $query = $uri->getQueryVariables();
        $this->assertEquals('GET', $req->method);
        $this->assertEquals('/stats/lead_report.json', $uri->getPath());
        $this->assertEquals(
            [
            'api_key' => 'abc',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'page' => '2',
            'limit' => '1000',
            'filter' => [
            'Stat.offer_id' => 3135,
            ],
            ], $query
        );
    }
    public function testCreateConversionReportRequestWithPageAndFormat()
    {
        $req = $this->client->createConversionReportRequest(date('Y-m-d'), 2, '500', 'csv');

        $uri = new \Net_URL2($req->uri);
        $query = $uri->getQueryVariables();
        $this->assertEquals('GET', $req->method);
        $this->assertEquals('/stats/lead_report.csv', $uri->getPath());
        $this->assertEquals(
            [
            'api_key' => 'abc',
            'start_date' => date('Y-m-d'),
            'end_date' => date('Y-m-d'),
            'page' => '2',
            'limit' => '500',
            'filter' => [
            'Stat.offer_id' => 3135,
            ],
            ], $query
        );
    }
}
