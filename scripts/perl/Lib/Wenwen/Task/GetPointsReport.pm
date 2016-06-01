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

    my $sum_survey_cost_points       = $self->get_survey_cost_points();
    my $sum_cps_chanet_cost_points   = $self->get_cps_chanet_cost_points();
    my $sum_cps_emar_cost_points     = $self->get_cps_emar_cost_points();
    my $sum_cps_duomai_cost_points   = $self->get_cps_duomai_cost_points();
    my $sum_cpa_offer99_cost_points  = $self->get_cpa_offer99_cost_points();
    my $sum_cpa_offerwow_cost_points = $self->get_cpa_offerwow_cost_points();

    my $total_cps_cost_points
        = $sum_cps_chanet_cost_points + $sum_cps_emar_cost_points + $sum_cps_duomai_cost_points;
    my $total_cpa_cost_points = $sum_cpa_offer99_cost_points + $sum_cpa_offerwow_cost_points;
    my $total_cost_points
        = $sum_survey_cost_points + $total_cps_cost_points + $total_cpa_cost_points;

    my $sum_survey_expense_points   = $self->get_survey_expense_points();
    my $sum_register_expense_points = $self->get_register_expense_points();

    my $total_expense_points = $sum_survey_expense_points + $sum_register_expense_points;

    my $sum_expired_points = $self->get_expired_points();

    my $sum_exchanged_alipay_points     = $self->get_exchanged_alipay_points();
    my $sum_exchanged_mobile_fee_points = $self->get_exchanged_mobile_fee_points();
    my $total_exchanged_points = $sum_exchanged_alipay_points + $sum_exchanged_mobile_fee_points;

    my $sum_cps_chanet_order_cost_points   = $self->get_cps_chanet_order_cost_points();
    my $sum_cps_emar_order_cost_points     = $self->get_cps_emar_order_cost_points();
    my $sum_cps_duomai_order_cost_points   = $self->get_cps_duomai_order_cost_points();
    my $sum_cpa_offer99_order_cost_points  = $self->get_cpa_offer99_order_cost_points();
    my $sum_cpa_offerwow_order_cost_points = $self->get_cpa_offerwow_order_cost_points();

    $buff = $buff . sprintf "Points Report from %s to %s \n", $self->from_date->strftime('%F %T'),
        $self->to_date->strftime('%F %T');
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Summary \n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Total Cost Points. \n=> %d\n", $total_cost_points;
    $buff = $buff . sprintf "Total Expense Points. \n=> %d\n", $total_expense_points;
    $buff = $buff . sprintf "Total Expired Points. \n=> %d\n", $sum_expired_points;
    $buff = $buff . sprintf "Total Exchanged Points. \n=> %d\n", $total_exchanged_points;
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Cost Details\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Survey Cost Points. \n=> %d\n", $sum_survey_cost_points;
    $buff = $buff . sprintf "CPS chanet Cost Points. \n=> %d\n", $sum_cps_chanet_cost_points;
    $buff = $buff . sprintf "CPS emar Cost Points. \n=> %d\n", $sum_cps_emar_cost_points;
    $buff = $buff . sprintf "CPS duomai Cost Points. \n=> %d\n", $sum_cps_duomai_cost_points;
    $buff = $buff . sprintf "CPA offer99 Cost Points. \n=> %d\n", $sum_cpa_offer99_cost_points;
    $buff = $buff . sprintf "CPA offerwow Cost Points. \n=> %d\n", $sum_cpa_offerwow_cost_points;
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Expense Details\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Survey Expense Points. \n=> %d\n", $sum_survey_expense_points;
    $buff = $buff . sprintf "Register Expense Points. \n=> %d\n", $sum_register_expense_points;
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Exchanged Details \n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Alipay. \n=> %d\n", $sum_exchanged_alipay_points;
    $buff = $buff . sprintf "Mobile Fee. \n=> %d\n", $sum_exchanged_mobile_fee_points;
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "Order Cost Details\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "CPS chanet Order Cost Points. \n=> %d\n",
        $sum_cps_chanet_order_cost_points;
    $buff = $buff . sprintf "CPS emar Order Cost Points. \n=> %d\n",
        $sum_cps_emar_order_cost_points;
    $buff = $buff . sprintf "CPS duomai Order Cost Points. \n=> %d\n",
        $sum_cps_duomai_order_cost_points;
    $buff = $buff . sprintf "CPA offer99 Order Cost Points. \n=> %d\n",
        $sum_cpa_offer99_order_cost_points;
    $buff = $buff . sprintf "CPA offerwow Order Cost Points. \n=> %d\n",
        $sum_cpa_offerwow_order_cost_points;
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
#   Cost Points for CPS - Chanet
##
sub get_cps_chanet_cost_points {
    my $self = shift;

    my $cps_chanet_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cps_chanet_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cps_chanet_cost_points;
}

##
#   Cost Points for CPS - Emar
##
sub get_cps_emar_cost_points {
    my $self = shift;

    my $cps_emar_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cps_emar_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cps_emar_cost_points;
}

##
#   Cost Points for CPS - Duomai
##
sub get_cps_duomai_cost_points {
    my $self = shift;

    my $cps_duomai_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cps_duomai_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cps_duomai_cost_points;
}

##
#   Cost Points for CPA - offer99
##
sub get_cpa_offer99_cost_points {
    my $self = shift;

    my $cpa_offer99_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cpa_offer99_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cpa_offer99_cost_points;
}

##
#   Cost Points for CPA - offerwow
##
sub get_cpa_offerwow_cost_points {
    my $self = shift;

    my $cpa_offerwow_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cpa_offerwow_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cpa_offerwow_cost_points;
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
#   Register Expense Points
##
sub get_register_expense_points {
    my $self = shift;

    my $survey_expense_points
        = Wenwen::Model::Service::PointsSummary->sum_register_expense_points($handle,
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

##
#   Order Cost Points for CPS - Chanet
##
sub get_cps_chanet_order_cost_points {
    my $self = shift;

    my $cps_chanet_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cps_chanet_order_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cps_chanet_cost_points;
}

##
#   Order Cost Points for CPS - Emar
##
sub get_cps_emar_order_cost_points {
    my $self = shift;

    my $cps_emar_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cps_emar_order_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cps_emar_cost_points;
}

##
#   Order Cost Points for CPS - Duomai
##
sub get_cps_duomai_order_cost_points {
    my $self = shift;

    my $cps_duomai_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cps_duomai_order_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cps_duomai_cost_points;
}

##
#   Order Cost Points for CPA - offer99
##
sub get_cpa_offer99_order_cost_points {
    my $self = shift;

    my $cpa_offer99_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cpa_offer99_order_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cpa_offer99_cost_points;
}

##
#   Order Cost Points for CPA - offerwow
##
sub get_cpa_offerwow_order_cost_points {
    my $self = shift;

    my $cpa_offerwow_cost_points
        = Wenwen::Model::Service::PointsSummary->sum_cpa_offerwow_order_cost_points($handle,
        $self->from_date, $self->to_date);
    return $cpa_offerwow_cost_points;
}

1;
