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

use constant REGISTER_CENTER => {
    'sop:checkout_survey_list' => 'Wenwen\\FrontendBundle\\Command\\CheckoutSurveyListCommand',
    'point:expire' => 'Wenwen\\FrontendBundle\\Command\\ExpirePointCommand',
    'mail:fulcrum_delivery_notification' => 'Wenwen\\FrontendBundle\\Command\\FulcrumDeliveryNotificationMailCommand',
    'gmo:member_list_csv' => 'Wenwen\\FrontendBundle\\Command\\FulcrumDeliveryNotificationMailCommand',
    'sop:push_basic_profile' => 'Wenwen\\FrontendBundle\\Command\\PushBasicProfileCommand',
    'user:reset_password' => 'Wenwen\\FrontendBundle\\Command\\ResetPasswordCommand',
    'mail:reset_password' => 'Wenwen\\FrontendBundle\\Command\\ResetPasswordMailCommand',
    'mail:signup_confirmation' => 'Wenwen\\FrontendBundle\\Command\\SignupConfirmationMailCommand',
    'mail:signup_success' => 'Wenwen\\FrontendBundle\\Command\\SignupSuccessMailCommand',
    'mail:sop_delivery_notification' => 'Wenwen\\FrontendBundle\\Command\\SopDeliveryNotificationMailCommand',
    'mail:ssi_delivery_notification_batch' => 'Wenwen\\FrontendBundle\\Command\\SsiDeliveryNotificationBatchMailCommand',
    'mail:ssi_delivery_notification' => 'Wenwen\\FrontendBundle\\Command\\SsiDeliveryNotificationMailCommand',
};

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
                class => $self->commandToClass($$row[4]),
            };
            $i++;
        }

        my %h;
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


sub commandToClass {
    my ($self, $command) = @_;
    my $class = REGISTER_CENTER->{$command};
    return defined($class) ? $class : 'Unregister';
}

1;
