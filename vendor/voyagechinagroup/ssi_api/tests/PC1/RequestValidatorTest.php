<?php

namespace VendorIntegration\SSI\tests\PC1;

class RequestValidatorTest extends \PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $validator = new \VendorIntegration\SSI\PC1\RequestValidator();
        $this->assertInstanceOf('\VendorIntegration\SSI\PC1\RequestValidator', $validator);
    }

    public function testIsValid()
    {
        $validator = \Phake::partialMock('\VendorIntegration\SSI\PC1\RequestValidator');

        \Phake::when($validator)->getErrors()->thenReturn([]);

        $this->assertTrue($validator->isValid());

        \Phake::when($validator)->getErrors()->thenReturn(['error' => 1]);
        $this->assertFalse($validator->isValid());
    }

    /**
     * @dataProvider invalidDataProvider
     */
    public function testValidateForInvalidData($input, $expected)
    {
        $request = new \VendorIntegration\SSI\PC1\Request();
        $request->loadJson(json_encode($input));

        $validator = new \VendorIntegration\SSI\PC1\RequestValidator($request);
        $validator->validate();

        $this->assertEquals($expected, $validator->getErrors());
        $this->assertFalse($validator->isValid());
    }

    public function testValidateForValidData()
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
                'respondentList' => ['test'],
                ]
            )
        );

        $validator = new \VendorIntegration\SSI\PC1\RequestValidator($request);
        $validator->validate();

        $this->assertEmpty($validator->getErrors());
        $this->assertTrue($validator->isValid());
    }

    public function invalidDataProvider()
    {
        return [
          [
            [],
            ['mailBatchId' => ['NOT_NULL' => 1],
            'startUrlHead' => ['NOT_NULL' => 1],
            'projectId' => ['NOT_NULL' => 1],
            'contactMethodId' => ['NOT_NULL' => 1],
            'respondentList' => ['NOT_NULL' => 1],
            ]
          ],
          [
            [
              'requestHeader' => [
                'contactMethodId' => 1,
                'mailBatchId' => 3,
              ],
              'startUrlHead' => 'String',
              'respondentList' => ['test'],
            ],
            ['projectId' => ['NOT_NULL' => 1]],
          ],
          [
            [
              'requestHeader' => [
                'projectId' => 1,
                'mailBatchId' => 3,
              ],
              'startUrlHead' => 'String',
              'respondentList' => ['test'],
            ],
            ['contactMethodId' => ['NOT_NULL' => 1]],
          ],
          [
            [
              'requestHeader' => [
                'contactMethodId' => 1,
                'projectId' => 2,
                'mailBatchId' => 3,
              ],
              'startUrlHead' => 'String',
              'respondentList' => [],
            ],
            ['respondentList' => ['NOT_NULL' => 1]],
          ],
        ];
    }
}
