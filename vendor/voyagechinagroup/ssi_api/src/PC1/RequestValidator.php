<?php

namespace VendorIntegration\SSI\PC1;

class RequestValidator
{
    private $request, $errors = [];

    public function __construct($request = null)
    {
        if (!is_null($request)) {
            $this->initialize($request);
        }
    }

    public function initialize(\VendorIntegration\SSI\PC1\Request $request)
    {
        $this->request = $request;
    }

    public function validate()
    {
        $errors = [];
        if (!$this->request->getProjectId()) {
            $errors['projectId']['NOT_NULL'] = '1';
        }
        if (!$this->request->getMailBatchId()) {
            $errors['mailBatchId']['NOT_NULL'] = '1';
        }
        if (!$this->request->getContactMethodId()) {
            $errors['contactMethodId']['NOT_NULL'] = '1';
        }
        if (!$this->request->getStartUrlHead()) {
            $errors['startUrlHead']['NOT_NULL'] = '1';
        }
        $respondent_list = $this->request->getRespondentList();
        if (!$respondent_list || !sizeof($respondent_list)) {
            $errors['respondentList']['NOT_NULL'] = '1';
        }
        $this->errors = $errors;
    }

    public function isValid()
    {
        return sizeof($this->getErrors()) === 0;
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
