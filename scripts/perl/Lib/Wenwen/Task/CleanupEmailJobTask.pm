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
        # delete finished jobs.
        my $sth = $dbh->prepare(qq{delete from jms_jobs where state='finished' and date(closedAt) < ?});
        $sth->execute($delete_before);

        # find out failed jobs.
        $sth = $dbh->prepare(qq{select id,state,queue,createdAt,command,args from jms_jobs where state='failed'});
        $sth->execute();

        ## prepare warning message for failed jobs to slack
        my @attachments;
        for my $row (@{$sth->fetchall_arrayref()}) {
            my @fields;
            push @fields, {
                title => 'id',
                value => $$row[0],
                short => 'true',
            };

            push @fields, {
                title => 'createdAt',
                value => $$row[3],
                short => 'true',
            };

            push @attachments, {
                title  => $$row[4],
                text   => 'This job failed. Please confirm and solve it.',
                color  => 'warning',
                fields => \@fields,
            };
        }
        if(@attachments){
            my $attachment_size = @attachments;
            if ($attachment_size <= 20) { # Number of attachments must <= 100
                $self->send_to_slack(encode_json({
                    text => __PACKAGE__,
                    attachments => \@attachments,
                }));
            } else {
                $self->send_to_slack(encode_json({text => __PACKAGE__ . "\n Too many failures that we won't display here."}));
            }
        }
    };

    if ($@) {
        $self->send_to_slack(encode_json({text => __PACKAGE__ . "\n Please check the reason of this failure and re-run this job."}));
        $self->send_to_slack(encode_json({text => $@}));
    }

    $dbh->disconnect;
}

sub send_to_slack {
    my ($self, $message) = @_;
    my $response = $self->ua->post(Wenwen::Model::get_slack_url, {payload => $message});
    die $response->status_line unless $response->is_success;
}

1;
