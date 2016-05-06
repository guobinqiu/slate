<?php

namespace VendorIntegration\SSI\PC1\Model\Query;

use \VendorIntegration\SSI\PC1\Constants;

class SsiRespondentQuery
{
    public static function retrieveValidRespondentRow($dbh, $ssi_respondent_id)
    {
        $stmt = $dbh->prepare(
            'SELECT * FROM ssi_respondent
        WHERE
          id = ?
          AND status_flag = ?'
        );
        $stmt->execute(array($ssi_respondent_id, Constants::SSI_RESPONDENT_STATUS_ACTIVE));
        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        return $row ? $row : null;
    }
}
