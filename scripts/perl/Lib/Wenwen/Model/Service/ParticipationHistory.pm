package Wenwen::Model::Service::ParticipationHistory;
use common::sense;

##
#   costs
##

# sop participation history
sub select_sop_participation_history {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $min = $from->strftime('%Y-%m-%d %H:%M:%S');
    my $max = $to->strftime('%Y-%m-%d %H:%M:%S');
    my @arr1;
    for (my $i=0; $i<10; $i++) {
      @arr1[$i] = "select a.survey_id, sum(b.`point`) points from survey_sop a
      join task_history0$i b
      on a.id = b.order_id
      where b.ocd_created_date >= '$min'
      and b.ocd_created_date < '$max'
      and b.task_type='9'
      and b.category_type='402'
      and b.order_type = 'Wenwen\\\\FrontendBundle\\\\Entity\\\\SurveySop'
      group by a.survey_id";
    }

    my @arr2;
    for (my $i=0; $i<10; $i++) {
      @arr2[$i] = "select a.survey_id, sum(b.`point`) points from survey_sop_participation_history a
      join task_history0$i b
      on a.id = b.order_id
      where b.ocd_created_date >= '$min'
      and b.ocd_created_date < '$max'
      and b.task_type='9'
      and b.category_type='402'
      and b.order_type = 'Wenwen\\\\FrontendBundle\\\\Entity\\\\SurveySopParticipationHistory'
      group by a.survey_id";
    }

    my @arr = (@arr1, @arr2);
    my $yyyymm = $from->strftime('%Y%m');
    my $sql = "select $yyyymm, t.survey_id, 'COST', sum(t.points) point from (" . join(" union all ", @arr) . ") t group by t.survey_id";
    print "$sql\n";

    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute() or die $dbh->errstr;
    my $result = $sth->fetchall_arrayref();

    return $result;
}

# cint participation history
sub select_cint_participation_history {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $min = $from->strftime('%Y-%m-%d %H:%M:%S');
    my $max = $to->strftime('%Y-%m-%d %H:%M:%S');
    my @arr;
    for (my $i=0; $i<10; $i++) {
      @arr[$i] = "select a.survey_id, sum(b.`point`) points from survey_cint_participation_history a
      join task_history0$i b
      on a.id = b.order_id
      where b.ocd_created_date >= '$min'
      and b.ocd_created_date < '$max'
      and b.task_type='9'
      and b.category_type='404'
      and b.order_type = 'Wenwen\\\\FrontendBundle\\\\Entity\\\\SurveyCintParticipationHistory'
      group by a.survey_id";
    }

    my $yyyymm = $from->strftime('%Y%m');
    my $sql = "select $yyyymm, 'Cint', t.survey_id, sum(t.points) point from (" . join(" union all ", @arr) . ") t group by t.survey_id";
    print "$sql\n";

    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute() or die $dbh->errstr;
    my $result = $sth->fetchall_arrayref();

    return $result;
}

# fulcrum participation history
sub select_fulcrum_participation_history {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $min = $from->strftime('%Y-%m-%d %H:%M:%S');
    my $max = $to->strftime('%Y-%m-%d %H:%M:%S');
    my @arr;
    for (my $i=0; $i<10; $i++) {
      @arr[$i] = "select sum(b.`point`) points from survey_fulcrum_participation_history a
      join task_history0$i b
      on a.id = b.order_id
      where b.ocd_created_date >= '$min'
      and b.ocd_created_date < '$max'
      and b.task_type='9'
      and b.category_type='405'
      and b.order_type = 'Wenwen\\\\FrontendBundle\\\\Entity\\\\SurveyFulcrumParticipationHistory'";
    }

    my $yyyymm = $from->strftime('%Y%m');
    my $sql = "select $yyyymm, 'Fulcrum', '-', sum(t.points) point from (" . join(" union all ", @arr) . ") t";
    print "$sql\n";

    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute() or die $dbh->errstr;
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
                count(*)*300 AS point
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
