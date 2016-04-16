<?php

namespace VendorIntegration\SSI\PC1\Model\Query;

class SsiProjectQuery
{
    public static function getRowById($dbh, $id)
    {
        $stmt = $dbh->prepare('SELECT * FROM ssi_project WHERE id = ?');
        $stmt->execute(array($id));

        $row = $stmt->fetch(\PDO::FETCH_ASSOC);
        $stmt->closeCursor();

        if ($row === false) {
            return null;
        }

        return $row;
    }

    public static function insertProject($dbh, $params)
    {
        foreach (array('id', 'status_flag') as $key) {
            if (!isset($params["$key"])) {
                throw new \Exception("$key is required");
            }
        }

        $stmt = $dbh->prepare(
            'INSERT INTO ssi_project
            (id, status_flag, created_at, updated_at)
          VALUES
            (?, ?, NOW(), NOW())'
        );

        $bindValues = array(
          $params['id'],
          $params['status_flag']
        );
        $stmt->execute($bindValues);
    }
}
