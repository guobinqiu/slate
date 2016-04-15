<?php

namespace Wenwen\AppBundle\Services;

use VendorIntegration\SSI\PC1\WebService\StatClient;

class SsiConversionReportIterator
{
    private $page = 1;
    private $client = null;
    private $date = null;
    private $limit = 1000;
    private $conversions = [];
    private $max = null;
    public function initialize(StatClient $client, $date)
    {
        $this->client = $client;
        $this->date = $date;
    }
    public function nextConversion()
    {
        # 1st page
        if ($this->max === null) {
            $res = $this->getConversionReport($this->page);
            $this->conversions = $res['data'];
            $this->max = is_null($res['totalNumRows']) ? 0 : $res['totalNumRows'];
        }
        # next page
        if (sizeof($this->conversions) === 0 && $this->page * $this->limit < $this->max) {
            ++$this->page;
            $res = $this->getConversionReport($this->page);
            $this->conversions = $res['data'];
        }

        return sizeof($this->conversions) ? array_shift($this->conversions) : null;
    }
    public function getConversionReport($page)
    {
        $req = $this->client->createConversionReportRequest($this->date, $this->page, $this->limit);
        $res = json_decode($req->send()->raw_body, true);
        if ($res['success'] === false) {
            throw \Exception('Failed to fetch conversion report: '.implode(', ', $res));
        }

        return $res;
    }
}
