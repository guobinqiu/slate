<?php
namespace VendorIntegration\SSI\PC1\Model\Query;

use \VendorIntegration\SSI\PC1\ProjectSurvey;
use \VendorIntegration\SSI\PC1\Constants;

class SsiProjectRespondentQuery
{
    public static function retrieveSurveyBySsiRespondentIdAndSsiProjectId($dbh, $ssi_respondent_id, $project_id)
    {
        $stmt = $dbh->prepare(
            'SELECT * FROM ssi_project_respondent
          WHERE
            ssi_respondent_id = ?
            AND ssi_project_id = ?'
        );
        $stmt->execute(array($ssi_respondent_id, $project_id));

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($row === false) {
            return null;
        }

        return new ProjectSurvey($row);
    }

    public static function insertRespondent($dbh, $ssi_respondent_id, $params)
    {
        $required = ['ssi_project_id', 'ssi_mail_batch_id', 'start_url_id', 'stash_data'];
        foreach ($required as $key) {
            if (!isset($params["$key"])) {
                throw new \Exception("$key is required");
            }
        }

        $stmt = $dbh->prepare(
            'INSERT INTO ssi_project_respondent (
              ssi_project_id,
              ssi_mail_batch_id,
              ssi_respondent_id,
              start_url_id,
              stash_data,
              created_at,
              updated_at
          ) VALUES (?, ?, ?, ?, ?, NOW(), NOW())
          ON DUPLICATE KEY UPDATE
              answer_status = ?,
              start_url_id = ?,
              stash_data = ?,
              updated_at = now()'
        );

        $stash_data = json_encode($params['stash_data']);
        $stmt->execute(
            [
            $params['ssi_project_id'],
            $params['ssi_mail_batch_id'],
            $ssi_respondent_id,
            $params['start_url_id'],
            $stash_data,
            Constants::SSI_PROJECT_RESPONDENT_STATUS_REOPENED,
            $params['start_url_id'],
            $stash_data,
            ]
        );

        $stmt->closeCursor();

        return $stmt->rowCount();
    }

    public static function retrieveSurveysForRespondent($dbh, $ssi_respondent_id)
    {
        $stmt = $dbh->prepare(
            'SELECT
                prj_res.*
            FROM ssi_project_respondent prj_res
            INNER JOIN ssi_project prj
                ON prj.id = prj_res.ssi_project_id
            WHERE
                prj.status_flag = ?
                AND
                prj_res.ssi_respondent_id = ?
                AND
                prj_res.answer_status < ?
                AND
                prj_res.updated_at >= DATE_SUB(NOW(), INTERVAL ? DAY)'
        );
        $stmt->execute(
            array(
            Constants::SSI_PROJECT_STATUS_ACTIVE,
            $ssi_respondent_id,
            Constants::SSI_PROJECT_RESPONDENT_STATUS_COMPLETE,
            Constants::SSI_PROJECT_SURVEY_AVAILABLE_DAYS,
            )
        );

        $items = array();
        while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
            $items[] = new ProjectSurvey($row);
        }
        $stmt->closeCursor();
        return $items;
    }

    public static function completeSurveysForRespondent($dbh, $ssi_respondent_id)
    {
        $stmt = $dbh->prepare(
            'UPDATE ssi_project_respondent prj_res
            INNER JOIN ssi_project prj
                ON prj.id = prj_res.ssi_project_id
            SET
                prj_res.answer_status = ?,
                prj_res.updated_at = NOW()
            WHERE
                prj.status_flag = ?
                AND
                prj_res.ssi_respondent_id = ?
                AND
                prj_res.updated_at >= DATE_SUB(NOW(), INTERVAL ? DAY)'
        );
        $stmt->execute(
            array(
            Constants::SSI_PROJECT_RESPONDENT_STATUS_COMPLETE,
            Constants::SSI_PROJECT_STATUS_ACTIVE,
            $ssi_respondent_id,
            Constants::SSI_PROJECT_SURVEY_AVAILABLE_DAYS,
            )
        );
        $stmt->closeCursor();

        return $stmt->rowCount();
    }

    public static function updateAnswerStatusForSurvey($dbh, $ssi_respondent_id, $project_id, $answer_status)
    {
        $stmt = $dbh->prepare(
            'UPDATE ssi_project_respondent
        SET
            answer_status = ?,
            updated_at = NOW()
        WHERE
          ssi_respondent_id = ?
          AND ssi_project_id = ?'
        );
        $stmt->execute(array($answer_status, $ssi_respondent_id, $project_id));
        $rows = $stmt->rowCount();
        $stmt->closeCursor();

        return $rows;
    }
}
