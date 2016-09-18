package Wenwen::Model::Service::PointsSummary;
use common::sense;

##
#   costs
##

# research survey cost
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
                (p.reason = 92 or  or p.reason between 402 and 499)
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

# cost for CPS - Chanet
sub sum_cps_chanet_cost_points {
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
                p.reason = 2
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

# cost for CPS - Emar
sub sum_cps_emar_cost_points {
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
                (p.reason = 19 or p.reason = 20)
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

# cost for CPS - Duomai
sub sum_cps_duomai_cost_points {
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
                p.reason = 23
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

# cost for CPA - offer99
sub sum_cpa_offer99_cost_points {
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
                (p.reason = 18 or p.reason = 201)
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

# cost for CPA - offerwow
sub sum_cpa_offerwow_cost_points {
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
                (p.reason = 17 or p.reason = 200)
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

##
#   expenses
##

# expense for quickpoll and profiling survey
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
                (p.reason = 93 or  or p.reason between 302 and 399)
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

# expense for register
sub sum_register_expense_points {
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
                p.reason = 32
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

##
#   expired points
##
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

##
#  exchanged points
##

# exchanged to alipay
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

# exchanged to mobile fee
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

##
#   Order Cost - order happened but not confirmed yet
##

# order cost for CPS - Chanet
sub sum_cps_chanet_order_cost_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(t.point)
            FROM task_history0${counter} t 
            WHERE 
                t.status >= 2 
                AND t.category_type = 2
                AND t.ocd_created_date >= ?
                AND t.ocd_created_date < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

# order cost for CPS - Emar
sub sum_cps_emar_order_cost_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(t.point)
            FROM task_history0${counter} t 
            WHERE 
                (t.category_type = 19 or t.category_type = 20) 
                AND t.ocd_created_date >= ?
                AND t.ocd_created_date < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

# order cost for CPS - Duomai
sub sum_cps_duomai_order_cost_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(t.point)
            FROM task_history0${counter} t 
            WHERE 
                t.category_type = 23
                AND t.ocd_created_date >= ?
                AND t.ocd_created_date < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

# order cost for CPA - offer99
sub sum_cpa_offer99_order_cost_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(t.point)
            FROM task_history0${counter} t 
            WHERE 
                (t.category_type = 18 or t.category_type = 201)
                AND t.ocd_created_date >= ?
                AND t.ocd_created_date < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute($from->strftime('%F %T'), $to->strftime('%F %T')) or die $dbh->errstr;
        my $sum = $sth->fetchall_arrayref()->[0][0];
        $total_sum += $sum;
        $counter++;
    }
    return $total_sum;
}

# order cost for CPA - offerwow
sub sum_cpa_offerwow_order_cost_points {
    my ($class, $handle, $from, $to) = @_;
    my $dbh = $handle->dbh;

    my $total_sum = 0;
    my $counter   = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                sum(t.point)
            FROM task_history0${counter} t 
            WHERE 
                (t.category_type = 17 or t.category_type = 200)
                AND t.ocd_created_date >= ?
                AND t.ocd_created_date < ?
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
