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

        $sth = $dbh->prepare(qq{select id,state,queue,createdAt,command,args from jms_jobs where state='failed' and maxRetries=3});
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
        $h{'class'} = 'Wenwen::Task::CleanupEmailJobTask';
        $h{'total'} = $i;
        $h{'rows'} = \@a;
#        print encode_json({text => encode_json(\%h)});
        $self->send_to_slack(encode_json({text => encode_json(\%h)}));
    };

    if ($@) {
        $self->send_to_slack(encode_json({text => $@}));
        $self->send_to_slack(encode_json({text => 'please check the reason of this failure and re-run this job'}));
    }

    $dbh->disconnect;
}

sub send_to_slack {
    my ($self, $message) = @_;
    my $response = $self->ua->post(Wenwen::Model::get_slack_url, {payload => $message});
    print $response->status_line unless $response->is_success;
}

1;
