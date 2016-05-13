package Wenwen::Model::Service::PointsSummary;
use common::sense;

sub sum_survey_cost_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(p.point_change_num)
            FROM point_history0${counter} p 
            WHERE 
                p.reason = 92
                AND p.create_time >= ?
                AND p.create_time < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

sub sum_survey_expense_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(p.point_change_num)
            FROM point_history0${counter} p 
            WHERE 
                p.reason = 93
                AND p.create_time >= ?
                AND p.create_time < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

sub sum_expired_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(p.point_change_num)
            FROM point_history0${counter} p 
            WHERE 
                p.reason = 15
                AND p.create_time >= ?
                AND p.create_time < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

sub sum_exchanged_alipay_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(p.point_change_num)
            FROM point_history0${counter} p 
            WHERE 
                p.reason = 11
                AND p.create_time >= ?
                AND p.create_time < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

sub sum_exchanged_mobile_fee_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(p.point_change_num)
            FROM point_history0${counter} p 
            WHERE 
                p.reason = 12
                AND p.create_time >= ?
                AND p.create_time < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

1;
