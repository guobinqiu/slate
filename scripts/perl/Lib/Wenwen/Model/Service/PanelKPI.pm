package Wenwen::Model::Service::PanelKPI;
use common::sense;

sub count_register_number {
    my ($class, $handle, $register_from, $register_to) = @_;

    my $sql = "
        SELECT
            count(*)
        FROM user
        WHERE
            user.register_complete_date >= ?
            AND user.register_complete_date < ?
    ";
    my $dbh = $handle->dbh;
    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute($register_from->strftime('%F %T'), $register_to->strftime('%F %T'))
        or die $dbh->errstr;
    my $count = $sth->fetchall_arrayref()->[0][0];
    return $count;
}

sub count_active_number {
    my ($class, $handle, $register_from, $register_to, $reward_from, $reward_to) = @_;

    my $dbh         = $handle->dbh;
    my $total_count = 0;
    my $counter     = 0;
    while ($counter < 10) {
        my $sql = "
            SELECT
                count(distinct u.id)
            FROM user u
            LEFT JOIN point_history0${counter} p 
            ON u.id = p.user_id 
            WHERE 
                u.register_complete_date >= ?
                AND u.register_complete_date < ?
                AND (p.reason = 92 or p.reason = 93)
                AND p.create_time >= ?
                AND p.create_time < ?
        ";

        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute(
            $register_from->strftime('%F %T'), $register_to->strftime('%F %T'),
            $reward_from->strftime('%F %T'),   $reward_to->strftime('%F %T')
        ) or die $dbh->errstr;
        my $count = $sth->fetchall_arrayref()->[0][0];
        $total_count += $count;
        $counter++;
    }
    return $total_count;
}

sub count_recent_30_day_inactivated {
    my ($class, $handle, $active_from, $active_to, $inactive_from, $inactive_to) = @_;

    my $dbh = $handle->dbh;

    my $total_count = 0;
    my $counter     = 0;

    while ($counter < 10) {
        my $sql = "
        SELECT count(distinct p.user_id)
        FROM point_history0${counter} p
        WHERE p.user_id in(
            SELECT distinct p.user_id 
            FROM point_history0${counter} p 
            WHERE 
            (p.reason = 92 or p.reason = 93) 
            AND p.create_time >= ? 
            AND p.create_time < ? 
            )
        AND 
        p.user_id not in(
            SELECT distinct p.user_id 
            FROM point_history0${counter} p 
            WHERE 
            (p.reason = 92 or p.reason = 93) 
            AND p.create_time >= ? 
            AND p.create_time < ? 
            )
    ";
        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->execute(
            $active_from->strftime('%F %T'),   $active_to->strftime('%F %T'),
            $inactive_from->strftime('%F %T'), $inactive_to->strftime('%F %T')
        ) or die $dbh->errstr;
        my $count = $sth->fetchall_arrayref()->[0][0];
        $total_count += $count;
        $counter++;
    }

    return $total_count;
}

sub count_inactive_register {
    my ($class, $handle, $register_from, $register_to, $reward_from, $reward_to) = @_;

    my $dbh         = $handle->dbh;
    my $total_count = 0;
    my $sql         = "
                SELECT 
                    count(distinct u.id, u.email, u.register_complete_date)
                FROM user u 
                WHERE 
                    u.register_complete_date >= ? 
                AND 
                    u.register_complete_date < ? ";
    my $counter = 0;
    while ($counter < 10) {
        $sql = $sql . "
                    AND 
                        u.id not in(
                            SELECT
                                distinct p.user_id 
                            FROM point_history0${counter} p 
                            WHERE 
                                (p.reason = 92 or p.reason = 93) 
                                AND p.create_time >= ? 
                                AND p.create_time < ? 
                            )";
        $counter++;
    }
    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->bind_param(1, $register_from->strftime('%F %T'));
    $sth->bind_param(2, $register_to->strftime('%F %T'));

    $counter = 0;
    while ($counter < 5) {
        my $bind_counter1 = $counter * 2 + 3;
        my $bind_counter2 = $counter * 2 + 4;
        $sth->bind_param($bind_counter1, $reward_from->strftime('%F %T'));
        $sth->bind_param($bind_counter2, $reward_to->strftime('%F %T'));
        $counter++;
    }

    $sth->execute() or die $dbh->errstr;
    my $count = $sth->fetchall_arrayref()->[0][0];

    return $count;
}

sub count_withdraw {
    my ($class, $handle, $withdraw_from, $withdraw_to) = @_;
    my $dbh = $handle->dbh;

    my $sql = "
                SELECT count(uw.id)
                    FROM user_withdraw uw
                WHERE
                    uw.created_at >= ?
                    AND
                    uw.created_at < ?
                ";
    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute($withdraw_from->strftime('%F %T'), $withdraw_to->strftime('%F %T'))
        or die $dbh->errstr;
    my $count = $sth->fetchall_arrayref()->[0][0];

    return $count;
}

sub count_blacklist {
    my ($class, $handle, $delete_from, $delete_to) = @_;
    my $dbh = $handle->dbh;

    my $sql = "
                SELECT count(u.id)
                    FROM user u
                WHERE
                    u.delete_date >= ?
                    AND
                    u.delete_date < ?
                ";
    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->execute($delete_from->strftime('%F %T'), $delete_to->strftime('%F %T'))
        or die $dbh->errstr;
    my $count = $sth->fetchall_arrayref()->[0][0];

    return $count;
}

sub count_late_active {
    my ($class, $handle, $active_from, $active_to, $inactive_from, $inactive_to) = @_;
    my $dbh = $handle->dbh;

    my $total_count = 0;
    my $counter     = 0;
    while ($counter < 10) {
        my $sql = "
                SELECT count(distinct p.user_id) 
                    FROM point_history0${counter} p 
                WHERE 
                    (p.reason = 92 or p.reason = 93) 
                    AND p.create_time >= ? 
                    AND p.create_time < ? 
                    AND p.user_id IN (
                        SELECT distinct p.user_id
                        FROM point_history0${counter} p
                            LEFT JOIN user u
                            ON p.user_id = u.id
                        WHERE
                            u.register_complete_date >= ?
                            AND u.register_complete_date < ?
                            AND p.user_id NOT IN(
                                SELECT distinct p.user_id
                                FROM point_history0${counter} p
                                WHERE
                                    p.create_time >= ?
                                    AND p.create_time < ?
                                    AND (p.reason = 92 OR p.reason = 93)
                                )
                        )
                ";
        my $sth = $dbh->prepare($sql) or die $dbh->errstr;
        $sth->bind_param(1, $active_from->strftime('%F %T'));
        $sth->bind_param(2, $active_to->strftime('%F %T'));
        $sth->bind_param(3, $inactive_from->strftime('%F %T'));
        $sth->bind_param(4, $inactive_to->strftime('%F %T'));
        $sth->bind_param(5, $inactive_from->strftime('%F %T'));
        $sth->bind_param(6, $inactive_to->strftime('%F %T'));
        $sth->execute() or die $dbh->errstr;
        my $count = $sth->fetchall_arrayref()->[0][0];
        $total_count += $count;
        $counter++;
    }
    return $total_count;
}

1;
