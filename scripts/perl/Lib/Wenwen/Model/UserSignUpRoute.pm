package Wenwen::Model::UserSignUpRoute;
use common::sense;

use SQL::Maker::Condition;

sub retrieve_route_summary {
    my ($class, $handle, $query) = @_;

    my $cond = SQL::Maker::Condition->new;
    $cond->add('1' => '1');
    $cond->add('user.register_complete_date' => { '>=' => $query->{register_complete_date_from} })
        if defined $query->{register_complete_date_from};
    $cond->add('user.register_complete_date' => { '<' => $query->{register_complete_date_to} })
        if defined $query->{register_complete_date_to};
    $cond->add('user_sign_up_route.source_route' => $query->{registration_route})
        if exists $query->{registration_route};

    my $sth = $handle->dbh->prepare(
        qq|
        SELECT
            COUNT(*) as `count`
            ,IFNULL(user_sign_up_route.source_route, 'NULL') as source_route
        FROM user
        LEFT JOIN user_sign_up_route
          ON ( user_sign_up_route.user_id = user.id )
        WHERE
            @{[$cond->as_sql]}
        GROUP BY source_route
        |
    );
    $sth->execute($cond->bind);

    my $res = $sth->fetchall_hashref('source_route');
    $sth->finish;

    $res;
}

1;
