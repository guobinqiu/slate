<?php
namespace VendorIntegration\SSI\PC1;

use \VendorIntegration\SSI\PC1\Request;
use \VendorIntegration\SSI\PC1\Model\Row\SsiProject;
use \VendorIntegration\SSI\PC1\Model\Query\SsiProjectQuery;
use \VendorIntegration\SSI\PC1\Model\Query\SsiProjectRespondentQuery;
use \VendorIntegration\SSI\PC1\Model\Query\SsiRespondentQuery;
use \VendorIntegration\SSI\PC1\Constants;

class RequestHandler
{
    private static $dbh = null;
    private $failedRespondentIds = [];
    private $succeededRespondentIds = [];
    private $unsubscribedRespondentIds = [];

    public function __construct($dbh)
    {
        self::$dbh = $dbh;
    }

    public function getFailedRespondentIds()
    {
        return $this->failedRespondentIds;
    }
    public function getSucceededRespondentIds()
    {
        return $this->succeededRespondentIds;
    }
    public function getUnsubscribedRespondentIds()
    {
        return $this->unsubscribedRespondentIds;
    }

    public function setUpProject(Request $request)
    {
        $row = SsiProjectQuery::getRowById(self::$dbh, $request->getProjectId());

        if (is_null($row)) {
            SsiProjectQuery::insertProject(
                self::$dbh, [
                'id' => $request->getProjectId(),
                'status_flag' => Constants::SSI_PROJECT_STATUS_ACTIVE,
                ]
            );
        }
    }

    public function setUpProjectRespondents(Request $request)
    {
        $failedRespondentIds = [];
        $succeededRespondentIds = [];
        $unsubscribedRespondentIds = [];

        while ($respondent = $request->nextRespondent()) {
            if (! $respondent['start_url_id']) {
                $failedRespondentIds[] = $respondent['respondent_id'];
                continue;
            }

            if (!preg_match('/\Awwcn-(\d+)\z/', $respondent['respondent_id'], $match)) {
                $unsubscribedRespondentIds[] = $respondent['respondent_id'];
                continue;
            }

            $ssi_respondent_id = $match[1];
            $ssi_respondent = SsiRespondentQuery::retrieveValidRespondentRow(self::$dbh, $ssi_respondent_id);
            if (!$ssi_respondent) {
                $unsubscribedRespondentIds[] = $respondent['respondent_id'];
                continue;
            }

            $res = SsiProjectRespondentQuery::insertRespondent(self::$dbh, $ssi_respondent_id, $respondent);
            if ($res) {
                $succeededRespondentIds[] = $respondent['respondent_id'];
            } else {
                $failedRespondentIds[] = $respondent['respondent_id'];
            }
        }

        $this->failedRespondentIds = $failedRespondentIds;
        $this->succeededRespondentIds = $succeededRespondentIds;
        $this->unsubscribedRespondentIds = $unsubscribedRespondentIds;
    }
}
