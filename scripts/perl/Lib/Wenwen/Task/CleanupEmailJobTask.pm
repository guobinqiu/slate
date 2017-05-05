package Wenwen::Task::CleanupEmailJobTask;

use common::sense;
use Moo;

has handle => (is => 'ro',);

sub delete_finished_by_date {

    my ($self, $first_day_of_this_month) = @_;

    my $dbh = $self->handle->dbh;

#    eval {
#        $dbh->begin_work;
        my $sth = $dbh->prepare(qq{delete from jms_jobs where state='finished' and date(createdAt) < ?})
            or die $dbh->errstr;
        $sth->execute($first_day_of_this_month) or die $sth->errstr;
        $sth->finish;

#        $dbh->commit;
#    };
#
#    if ($@) {
#        $dbh->rollback;
#    }

    $dbh->disconnect();
}

1;
