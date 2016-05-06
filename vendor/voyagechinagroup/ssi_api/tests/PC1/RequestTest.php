<?php

namespace VendorIntegration\SSI\tests\PC1;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $request = new \VendorIntegration\SSI\PC1\Request();
        $this->assertInstanceOf('\VendorIntegration\SSI\PC1\Request', $request);
    }

    public function testLoadJson()
    {
        $request = new \VendorIntegration\SSI\PC1\Request();
        $request->loadJson(
            json_encode(
                [
                'requestHeader' => 'Header',
                'startUrlHead' => 'String',
                'respondentList' => ['key' => 'value'],
                ]
            )
        );
        $this->assertEquals('Header', $request->getRequestHeader());
        $this->assertEquals('String', $request->getStartUrlHead());
        $this->assertEquals(['key' => 'value'], $request->getRespondentList());
    }

    public function testGetterForRequestHeader()
    {
        $request = new \VendorIntegration\SSI\PC1\Request();

        $this->assertNull($request->getContactMethodId());
        $this->assertNull($request->getProjectId());
        $this->assertNull($request->getMailBatchId());

        $request->loadJson(
            json_encode(
                [
                'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 2,
                'mailBatchId' => 3,
                ],
                'startUrlHead' => 'String',
                'respondentList' => ['key' => 'value'],
                ]
            )
        );

        $this->assertEquals(1, $request->getContactMethodId());
        $this->assertEquals(2, $request->getProjectId());
        $this->assertEquals(3, $request->getMailBatchId());
    }

    public function testCreateNextRespondent()
    {
        $request = new \VendorIntegration\SSI\PC1\Request();
        $request->loadJson(
            json_encode(
                [
                'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 2,
                'mailBatchId' => 3,
                ],
                'startUrlHead' => 'String',
                'respondentList' => [
                [
                'respondentId' => 'wwcn-473',
                'startUrlId' => 'c37xkNG9WDnfTS6Y6IYJ1h3tECGN8vZn',
                ],
                [
                'respondentId' => 'wwcn-474',
                'startUrlId' => 'hogefuga',
                ],
                ],
                ]
            )
        );

        $this->assertEquals(
            [
            'ssi_project_id' => 2,
            'ssi_mail_batch_id' => 3,
            'respondent_id' => 'wwcn-473',
            'start_url_id' => 'c37xkNG9WDnfTS6Y6IYJ1h3tECGN8vZn',
            'stash_data' => [
            'contactMethodId' => 1,
            'startUrlHead' => 'String',
            ],
            ], $request->nextRespondent(), '1st'
        );

        $this->assertEquals(
            [
            'ssi_project_id' => 2,
            'ssi_mail_batch_id' => 3,
            'respondent_id' => 'wwcn-474',
            'start_url_id' => 'hogefuga',
            'stash_data' => [
            'contactMethodId' => 1,
            'startUrlHead' => 'String',
            ],
            ], $request->nextRespondent(), '2nd'
        );

        $this->assertNull($request->nextRespondent());
    }
}
