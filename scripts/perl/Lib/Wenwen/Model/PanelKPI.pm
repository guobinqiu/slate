package Wenwen::Model::PanelKPI;
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
    $sth->bind_param(1, $register_from);
    $sth->bind_param(2, $register_to);
    $sth->execute() or die $dbh->errstr;
    my $count = $sth->fetchall_arrayref()->[0][0];
    return $count;
}

sub count_active_number {
    my ($class, $handle, $register_from, $register_to, $reward_from, $reward_to) = @_;
    
    my $dbh = $handle->dbh;
    my $total_count = 0;
    my $counter = 0;
    while ( $counter < 10 ) {
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
        $sth->bind_param(1, $register_from);
        $sth->bind_param(2, $register_to);
        $sth->bind_param(3, $reward_from);
        $sth->bind_param(4, $reward_to);
        $sth->execute() or die $dbh->errstr;
        my $count = $sth->fetchall_arrayref()->[0][0];
        $total_count += $count;
        $counter++;
    }
    return $total_count;
}


sub count_recent_30_day_inactivated {
    my ($class, $handle, $active_from, $active_to, $inactive_from, $inactive_to) = @_;
    
    my $dbh = $handle->dbh;
    my $sql = "
        SELECT count(distinct p.user_id)
        FROM point_history09 p
        WHERE p.user_id in(
            SELECT distinct p.user_id 
            FROM point_history09 p 
            WHERE 
            (p.reason = 92 or p.reason = 93) 
            AND p.create_time >= ? 
            AND p.create_time < ? 
            )
        AND 
        p.user_id not in(
            SELECT distinct p.user_id 
            FROM point_history09 p 
            WHERE 
            (p.reason = 92 or p.reason = 93) 
            AND p.create_time >= ? 
            AND p.create_time < ? 
            )
    ";
    my $dbh = $handle->dbh;
    my $sth = $dbh->prepare($sql) or die $dbh->errstr;
    $sth->bind_param(1, $active_from);
    $sth->bind_param(2, $active_to);
    $sth->bind_param(3, $inactive_from);
    $sth->bind_param(4, $inactive_to);
    $sth->execute() or die $dbh->errstr;
    my $count = $sth->fetchall_arrayref()->[0][0];
    return $count;
}

sub count_inactive_register {
    my ($class, $handle, $register_from, $register_to, $reward_from, $reward_to) = @_;
    
    my $dbh = $handle->dbh;
    my $total_count = 0;
    my $sql = "
                SELECT 
                    distinct u.id, u.email, u.register_complete_date
                FROM user u 
                WHERE 
                    u.register_complete_date >= ? 
                AND 
                    u.register_complete_date < ? ";
    my $counter = 0;
    while ( $counter < 10 ) {
        $sql = $sql."
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
        $counter ++;
    }
    say "count_inactive_register $sql";
    my $sth = $dbh->prepare($sql) or die $dbh->errstr; 
    $sth->bind_param(1, $register_from);
    $sth->bind_param(2, $register_to);
    
    $counter = 0;
    while ($counter < 5) {
        my $tmp = 
        say "count_inactive_register binding ".$counter*2+3;
        $sth->bind_param($counter*2+3, $reward_from);
        say "count_inactive_register binding ".$counter*2+4;
        $sth->bind_param($counter*2+4, $reward_to);
        $counter++;
    }

    $dbh->trace('SQL');
    
    $sth->execute() or die $dbh->errstr;
    my $count = $sth->fetchall_arrayref()->[0][0];
    
    return $count;    
}


1;