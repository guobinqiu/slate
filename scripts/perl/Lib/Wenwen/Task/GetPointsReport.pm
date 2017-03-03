package Wenwen::Task::GetPointsReport;

use common::sense;

use Moo;
use Time::Piece ();
use Time::Piece::Plus;
use Time::Seconds;

use Wenwen::Model;
use Wenwen::Model::Service::PointsSummary;
use Wenwen::Task::ActiveRatio;

use constant SIGNUP = 300;           # (+) 完成注册获得积分
use constant QUICK_POLL = 301;       # (+) 快速问答
use constant SOP_EXPENSE = 302;      # (+) 属性问卷，IR CHECK等 (快速问答  ,アンケート回答（自社）61)
use constant SSI_EXPENSE = 303;      # (+) SSI AGREEMENT PRESCREEN等
use constant CINT_EXPENSE = 304;     # (+) Cint AGREEMENT
use constant FULCRUM_EXPENSE = 305;  # (+) Fulcrum AGREEMENT
use constant SURVEY_PARTNER_EXPENSE = 306;     # (+) 回答survey partner的实际商业问卷
use constant EVENT_INVITE_SIGNUP = 380; # 邀请注册加积分
use constant EVENT_INVITE_SURVEY = 381; # 做问卷给邀请人加积分
use constant EVENT_PRIZE = 382; # 抽奖活动
use constant EVENT_SIGNIN = 383; # 签到

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

    # survey_expense_points的细分项
    my $sum_survey_expense_points_signup = $self->get_survey_expense_points_by_category_type(SIGNUP);
    my $sum_survey_expense_points_quick_poll = $self->get_survey_expense_points_by_category_type(QUICK_POLL);
    my $sum_survey_expense_points_sop = $self->get_survey_expense_points_by_category_type(SOP_EXPENSE);
    my $sum_survey_expense_points_ssi = $self->get_survey_expense_points_by_category_type(SSI_EXPENSE);
    my $sum_survey_expense_points_cint = $self->get_survey_expense_points_by_category_type(CINT_EXPENSE);
    my $sum_survey_expense_points_fulcrum = $self->get_survey_expense_points_by_category_type(FULCRUM_EXPENSE);
    my $sum_survey_expense_points_survey_partner = $self->get_survey_expense_points_by_category_type(SURVEY_PARTNER_EXPENSE);
    my $sum_survey_expense_points_invite_signup = $self->get_survey_expense_points_by_category_type(EVENT_INVITE_SIGNUP);
    my $sum_survey_expense_points_invite_survey = $self->get_survey_expense_points_by_category_type(EVENT_INVITE_SURVEY);
    my $sum_survey_expense_points_event_prize = $self->get_survey_expense_points_by_category_type(EVENT_PRIZE);
    my $sum_survey_expense_points_event_signin = $self->get_survey_expense_points_by_category_type(EVENT_SIGNIN);

    my $sum_register_expense_points = $self->get_register_expense_points();

    my $total_expense_points = $sum_survey_expense_points + $sum_register_expense_points;

    my $sum_expired_points = $self->get_expired_points();

    my $sum_exchanged_alipay_points     = $self->get_exchanged_alipay_points();
    my $sum_exchanged_mobile_fee_points = $self->get_exchanged_mobile_fee_points();
    my $total_exchanged_points = $sum_exchanged_alipay_points + $sum_exchanged_mobile_fee_points;
    my $total_rest_points = $self->get_total_rest_points();

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
    $buff = $buff . sprintf "Total Rest Points. \n=> %d\n", $total_rest_points;
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
    $buff = $buff . sprintf "Survey Expense Points(signup). \n=> %d\n", $sum_survey_expense_points_signup;
    $buff = $buff . sprintf "Survey Expense Points(quick_poll). \n=> %d\n", $sum_survey_expense_points_quick_poll;
    $buff = $buff . sprintf "Survey Expense Points(sop). \n=> %d\n", $sum_survey_expense_points_sop;
    $buff = $buff . sprintf "Survey Expense Points(ssi). \n=> %d\n", $sum_survey_expense_points_ssi;
    $buff = $buff . sprintf "Survey Expense Points(cint). \n=> %d\n", $sum_survey_expense_points_cint;
    $buff = $buff . sprintf "Survey Expense Points(fulcrum). \n=> %d\n", $sum_survey_expense_points_fulcrum;
    $buff = $buff . sprintf "Survey Expense Points(survey_partner). \n=> %d\n", $sum_survey_expense_points_survey_partner;
    $buff = $buff . sprintf "Survey Expense Points(invite_signup). \n=> %d\n", $sum_survey_expense_points_invite_signup;
    $buff = $buff . sprintf "Survey Expense Points(invite_survey). \n=> %d\n", $sum_survey_expense_points_invite_survey;
    $buff = $buff . sprintf "Survey Expense Points(event_prize). \n=> %d\n", $sum_survey_expense_points_event_prize;
    $buff = $buff . sprintf "Survey Expense Points(event_signin). \n=> %d\n", $sum_survey_expense_points_event_signin;
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
#   Survey Expense Points by Category Type
##
sub get_survey_expense_points_by_category_type {
    my $self = shift;
    my $category_type = shift;

    my $survey_expense_points
        = Wenwen::Model::Service::PointsSummary->sum_survey_expense_points_by_category_type($handle,
        $self->from_date, $self->to_date, $category_type);
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

##
#   Total Rest Points
##
sub get_total_rest_points {
    my $self = shift;

    my $total_rest_points
        = Wenwen::Model::Service::PointsSummary->sum_total_rest_points($handle);
    return $total_rest_points;
}

1;
