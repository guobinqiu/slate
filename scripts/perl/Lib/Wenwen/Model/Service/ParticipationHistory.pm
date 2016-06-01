package Wenwen::Model::Service::ParticipationHistory;
use common::sense;

##
#   costs
##

# sop participation history
sub select_sop_participation_history {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $sql = "
            SELECT
                DATE_FORMAT(sp.created_at, '%Y%m') AS yyyymm,
                sp.partner_app_project_id AS app_project_id, 
                CASE sp.type 
                    WHEN 92 THEN 'COST'
                    WHEN 93 THEN 'EXPENSE'
                    ELSE 'ERROR'
                END AS point_type, 
                sum(sp.point) AS point
            FROM sop_research_survey_participation_history sp 
            WHERE 
                sp.created_at >= ?
                AND sp.created_at < ?
            GROUP BY
                DATE_FORMAT(sp.created_at, '%Y%m'), sp.partner_app_project_id, sp.type
            ORDER BY
                DATE_FORMAT(sp.created_at, '%Y%m'), sp.partner_app_project_id, sp.type
        ";

    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
    my $result = $sth->fetchall_arrayref();

    return $result;
}

# cint participation history
sub select_cint_participation_history {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $sql = "
            SELECT
                DATE_FORMAT(cp.created_at, '%Y%m') AS yyyymm, 
                'Cint' AS 'API  Type', 
                cp.cint_project_id AS 'Project ID', 
                sum(cp.point) AS point 
            FROM cint_research_survey_participation_history cp 
            WHERE 
                cp.created_at >= ? 
                AND cp.created_at < ? 
            GROUP BY
                DATE_FORMAT(cp.created_at, '%Y%m'), cp.cint_project_id 
            ORDER BY
                DATE_FORMAT(cp.created_at, '%Y%m'), cp.cint_project_id
        ";

    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
    my $result = $sth->fetchall_arrayref();

    return $result;
}

# fulcrum participation history
sub select_fulcrum_participation_history {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $sql = "
            SELECT
                DATE_FORMAT(fp.created_at, '%Y%m') AS yyyymm,
                'Fulcrum' AS 'API Type',
                '-' AS 'Project ID', 
                sum(fp.point) AS point
            FROM fulcrum_research_survey_participation_history fp 
            WHERE 
                fp.created_at >= ?
                AND fp.created_at < ?
            GROUP BY
                DATE_FORMAT(fp.created_at, '%Y%m')
            ORDER BY
                DATE_FORMAT(fp.created_at, '%Y%m')
        ";

    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
    my $result = $sth->fetchall_arrayref();

    return $result;
}

# ssi participation history
sub select_ssi_participation_history {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $sql = "
            SELECT
                DATE_FORMAT(sp.created_at, '%Y%m') AS yyyymm,
                'SSI' AS 'API Type',
                '-' AS 'Project ID', 
                count(*)*180 AS point
            FROM ssi_project_participation_history sp 
            WHERE 
                sp.created_at >= ?
                AND sp.created_at < ?
            GROUP BY
                DATE_FORMAT(sp.created_at, '%Y%m')
            ORDER BY
                DATE_FORMAT(sp.created_at, '%Y%m')
        ";

    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
    my $result = $sth->fetchall_arrayref();

    return $result;
}

1;
