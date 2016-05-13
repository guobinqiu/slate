package Wenwen::Task::GetPointsReport;

use common::sense;

use Moo;
use Time::Piece ();
use Time::Piece::Plus;
use Time::Seconds;

use Wenwen::Model;
use Wenwen::Model::Service::PointsSummary;
use Wenwen::Task::ActiveRatio;

has base_date => (is => 'ro',);
has from_date => (is => 'ro',);
has to_date   => (is => 'ro',);

my $handle;

sub BUILDARGS {

    # Todo isolate dsn etc. from here
    my ($self, %args) = @_;

    my $base_date = $args{base_date};

    $args{base_date} = $self->init_base_date($base_date);
    $args{to_date}   = $self->init_to_date($base_date);
    $args{from_date} = $self->init_from_date($base_date);

    $handle = Wenwen::Model->create_handle();

    return \%args;
}

sub init_base_date {
    my ($self, $base_date) = @_;

    if (defined($base_date) && ref $base_date eq 'Time::Piece') {
        return $base_date;
    }
    else {
        return Time::Piece->strptime(Time::Piece::Plus->today, '%Y-%m-01 00:00:00');
    }
}

sub init_from_date {
    my ($self, $base_date) = @_;

    if (defined($base_date) && ref $base_date eq 'Time::Piece') {
        return $base_date->add_months(-1);
    }
    else {
        return Time::Piece->strptime(Time::Piece::Plus->today, '%Y-%m-01 00:00:00');
    }
}

sub init_to_date {
    my ($self, $base_date) = @_;

    if (defined($base_date) && ref $base_date eq 'Time::Piece') {
        return $base_date;
    }
    else {
        return Time::Piece->strptime(Time::Piece::Plus->today, '%Y-%m-01 00:00:00')->add_months(-1);
    }
}

sub do_task {
    my $self = shift;
    my $buff = "\n";

    my $sum_survey_cost_points          = $self->get_survey_cost_points();
    my $sum_survey_expense_points       = $self->get_survey_expense_points();
    my $sum_expired_points              = $self->get_expired_points();
    my $sum_exchanged_alipay_points     = $self->get_exchanged_alipay_points();
    my $sum_exchanged_mobile_fee_points = $self->get_exchanged_mobile_fee_points();

    $buff = $buff . sprintf "Report from %s to %s \n\n", $self->from_date->strftime('%F %T'),
        $self->to_date->strftime('%F %T');
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Points Summary \n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Survey Cost Points. \n=> %d\n", $sum_survey_cost_points;
    $buff = $buff . sprintf "Survey Expense Points. \n=> %d\n", $sum_survey_expense_points;
    $buff = $buff . sprintf "Expired Points. \n=> %d\n", $sum_expired_points;
    $buff = $buff . sprintf "Total Exchanged Points. \n=> %d\n\n",
        $sum_exchanged_alipay_points + $sum_exchanged_mobile_fee_points;

    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Exchanged Details \n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Alipay. \n=> %d\n", $sum_exchanged_alipay_points;
    $buff = $buff . sprintf "Mobile Fee. \n=> %d\n", $sum_exchanged_mobile_fee_points;

    $buff = $buff . sprintf "\n";

    return $buff;
}

##
#   Survey Cost Points
##
sub get_survey_cost_points {
    my $self = shift;

    my $survey_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_survey_cost_points($handle, $self->from_date,
        $self->to_date);
    return $survey_cost_points;
}

##
#   Survey Expense Points
##
sub get_survey_expense_points {
    my $self = shift;

    my $survey_expense_points
        = Wenwen::Model::Service::PointsSummary->sum_survey_expense_points($handle,
        $self->from_date, $self->to_date);
    return $survey_expense_points;
}

##
#   Expired Points
##
sub get_expired_points {
    my $self = shift;

    my $expired_points
        = Wenwen::Model::Service::PointsSummary->sum_expired_points($handle, $self->from_date,
        $self->to_date);
    return $expired_points;
}

##
#   Exchanged alipay Points
##
sub get_exchanged_alipay_points {
    my $self = shift;

    my $exchanged_alipay_points
        = Wenwen::Model::Service::PointsSummary->sum_exchanged_alipay_points($handle,
        $self->from_date, $self->to_date);
    return $exchanged_alipay_points;
}

##
#   Exchanged alipay Points
##
sub get_exchanged_mobile_fee_points {
    my $self = shift;

    my $exchanged_alipay_points
        = Wenwen::Model::Service::PointsSummary->sum_exchanged_mobile_fee_points($handle,
        $self->from_date, $self->to_date);
    return $exchanged_alipay_points;
}

1;
