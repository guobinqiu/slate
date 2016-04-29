<?php

namespace VendorIntegration\SSI\PC1;

class Request
{
    private $requestHeader, $startUrlHead, $respondentList;

    public function __construct($data = null)
    {
    }

    public function loadJson($json = '{}')
    {
        $params = json_decode($json, true);
        $this->requestHeader = isset($params['requestHeader']) ? $params['requestHeader'] : null;
        $this->startUrlHead = isset($params['startUrlHead']) ? $params['startUrlHead'] : null;
        $this->respondentList = isset($params['respondentList']) ? $params['respondentList'] : null;
    }

    public function getRequestHeader()
    {
        return $this->requestHeader;
    }
    public function getStartUrlHead()
    {
        return $this->startUrlHead;
    }
    public function getRespondentList()
    {
        return $this->respondentList;
    }

    public function getContactMethodId()
    {
        $request_header = $this->getRequestHeader();

        return isset($request_header['contactMethodId']) ? $request_header['contactMethodId'] : null;
    }
    public function getMailBatchId()
    {
        $request_header = $this->getRequestHeader();

        return isset($request_header['mailBatchId']) ? $request_header['mailBatchId'] : null;
    }
    public function getProjectId()
    {
        $request_header = $this->getRequestHeader();

        return isset($request_header['projectId']) ? $request_header['projectId'] : null;
    }

    public function nextRespondent()
    {
        if (sizeof($this->respondentList) === 0) {
            return null;
        }

        $respondent = array_shift($this->respondentList);

        return [
          'ssi_project_id' => $this->getProjectId(),
          'ssi_mail_batch_id' => $this->getMailBatchId(),
          'respondent_id' => $respondent['respondentId'],
          'start_url_id' => $respondent['startUrlId'],
          'stash_data' => [
            'contactMethodId' => $this->getContactMethodId(),
            'startUrlHead' => $this->getStartUrlHead(),
          ],
        ];
    }
}
