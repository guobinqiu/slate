<?php

namespace VendorIntegration\SSI\PC1;
use \VendorIntegration\SSI\PC1\Constants;

class ProjectSurvey
{
    private $item;

    public function __construct($item)
    {
        $this->item = $item;
    }
    public function getId()
    {
        return $this->item['id'];
    }
    public function getSsiProjectId()
    {
        return $this->item['ssi_project_id'];
    }
    public function getSsiRespondentId()
    {
        return $this->item['ssi_respondent_id'];
    }
    public function getStartUrlId()
    {
        return $this->item['start_url_id'];
    }
    public function getAnswerStatus()
    {
        return $this->item['answer_status'];
    }
    public function getCreatedAt()
    {
        return new \DateTime($this->item['created_at']);
    }
    public function getUpdatedAt()
    {
        return new \DateTime($this->item['updated_at']);
    }
    public function getStashData()
    {
        return json_decode($this->item['stash_data'], true);
    }
    public function isOpen()
    {
        $answer_status = $this->getAnswerStatus();
        return $answer_status < Constants::SSI_PROJECT_RESPONDENT_STATUS_COMPLETE;
    }
    public function getStartUrl()
    {
        $stash = $this->getStashData();
        if (!isset($stash['startUrlHead']) || !$stash['startUrlHead']) {
            throw new \Exception('start_url_head does not exist');
        }
        if (!$this->getStartUrlId()) {
            throw new \Exception('start_url_id does not exist');
        }
        return $stash['startUrlHead']. $this->getStartUrlId();
    }
}
