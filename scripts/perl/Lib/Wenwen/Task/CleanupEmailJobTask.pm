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

    my ($self, $delete_before) = @_;

    my $dbh = $self->handle->dbh;

    eval {
        my $sth = $dbh->prepare(qq{delete from jms_jobs where state='finished' and date(closedAt) < ?});
        $sth->execute($delete_before);

        $sth = $dbh->prepare(qq{select id,state,queue,closedAt,command,args from jms_jobs where state='failed' and maxRetries=3});
        $sth->execute();

        my @a;
        my $i = 0;
        for my $row (@{$sth->fetchall_arrayref()}) {
            push @a, {
                id => $$row[0],
                state => $$row[1],
                queue => $$row[2],
                createAt => $$row[3],
                command => $$row[4],
            };
            $i++;
        }

        my %h;
        $h{'total'} = $i;
        $h{'rows'} = \@a;
        $self->send_to_slack(encode_json({text => __PACKAGE__ . "\n Found failed jobs. Please solve them.\n" . encode_json(\%h)}));
    };

    if ($@) {
        $self->send_to_slack(encode_json({text => __PACKAGE__ . "\n please check the reason of this failure and re-run this job"}));
        $self->send_to_slack(encode_json({text => $@}));
    }

    $dbh->disconnect;
}

sub send_to_slack {
    my ($self, $message) = @_;
    my $response = $self->ua->post(Wenwen::Model::get_slack_url, {payload => $message});
    print $response->status_line unless $response->is_success;
}

1;
