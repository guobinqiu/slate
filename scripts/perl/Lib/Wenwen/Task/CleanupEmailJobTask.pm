package Wenwen::Task::CleanupEmailJobTask;

use common::sense;
use Moo;
use LWP::UserAgent;
use LWP::Protocol::https;
use JSON::XS qw(encode_json);
use Wenwen::Model;

has handle => (
    is => 'ro',
);

has ua => (
    is      => 'lazy',
    default => sub {
        my $ua = LWP::UserAgent->new;
        $ua->agent(__PACKAGE__);
        $ua;
    },
);

sub delete_finished_before_date {

    my ($self, $first_day_of_this_month) = @_;

    my $dbh = $self->handle->dbh;

    eval {
        my $sth = $dbh->prepare(qq{delete from jms_jobs where state='finished' and date(createdAt) < ?});
        $sth->execute($first_day_of_this_month);

        $sth = $dbh->prepare(qq{select * from jms_jobs where state='failed'});
        $sth->execute();
        if ($sth->rows > 0) {
            $self->send_to_slack(encode_json({text => encode_json($sth->fetchall_arrayref())}));
        };
    };

    if ($@) {
        $self->send_to_slack(encode_json({text => $@}));
    }

    $dbh->disconnect;
}

sub send_to_slack {
    my ($self, $data) = @_;
    my $response = $self->ua->post(Wenwen::Model::get_slack_url, {payload => $data});
    print $response->status_line unless $response->is_success;
}

1;
