<?php

namespace VendorIntegration\SSI\PC1\WebService;

use \Httpful\Request;

class StatClient
{
    const HOST = 'ssi.hasoffers.com';

    private $apiKey = null;
    private $offerId = 2189; // 1346 API_USD (91wenwen)

    public function __construct($apiKey, $offerId = null)
    {
        $this->apiKey = $apiKey;

        if (!is_null($offerId)) {
            $this->offerId = $offerId;
        }
    }

    public function createConversionReportRequest($date, $page = 1, $limit = 1000, $format = 'json')
    {
        $url = sprintf('https://%s/stats/lead_report.%s', self::HOST, $format);

        $uri = new \Net_URL2($url);
        $uri->setQueryVariables(
            [
            'api_key' => $this->apiKey,
            'start_date' => $date,
            'end_date' => $date,
            'page' => $page,
            'limit' => $limit,
            'filter' => [
            'Stat.offer_id' => $this->offerId,
            ],
            ]
        );

        return \Httpful\Request::get($uri->getURL());
    }
}
