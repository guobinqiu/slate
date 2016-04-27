package Wenwen::Task::GetPerformanceReport;

use common::sense;

use Moo;
use Time::Piece ();
use Time::Piece::Plus;
use Time::Seconds;

use Wenwen::Model;
use Wenwen::Model::Service::PanelKPI;
use Wenwen::Task::ActiveRatio;

has base_date => (is => 'ro',);

my $handle;

sub BUILDARGS {

    # Todo isolate dsn etc. from here
    my ($self, %args) = @_;

    my $base_date = $args{base_date};

    $args{base_date} = $self->init_base_date($base_date);

    $handle = Wenwen::Model->create_handle();

    return \%args;
}

sub init_base_date {
    my ($self, $base_date) = @_;

    if (defined($base_date) && ref $base_date eq 'Time::Piece') {
        return $base_date;
    }
    else {
        return Time::Piece::Plus->today;
    }
}

sub do_task {
    my $self = shift;
    my $buff = "\n";

    my $number_6au_bom             = $self->get_numbers_of_6au_bom();
    my $registered_number          = $self->get_inactivated_in_recent_30_day();
    my $newly_registered_user      = $self->get_newly_registered_user();
    my $inactive_number            = $self->get_dead_dogs();
    my $withdraw_number            = $self->get_withdraw_in_30_days();
    my $delete_number              = $self->get_blacklist_in_30_days();
    my $late_active_number         = $self->get_late_active_in_30_days();
    my $number_6au_eom             = $self->get_numbers_of_6au_eom();
    my @recent_30_day_active_ratio = $self->get_recent_30_day_active_ratio();
    my @recent_daily_active_ratio  = $self->get_recent_daily_active_ratio();

    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "KPI \n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "6AU BOM: Number of 6AU as of 31 days ago (Base). \n=> %d\n",
        $number_6au_bom->reward_number;
    $buff = $buff . sprintf "Inactive: Inactiated in recent 30 days (-). \n=> %d\n",
        $registered_number;
    $buff
        = $buff
        . sprintf
        "New: Newly registered users (including not active) within 30 days (+). \n=> %d\n",
        $newly_registered_user->register_number;
    $buff
        = $buff
        . sprintf
        "Dead Dog: Newly registered users who have NOT earned any activation points (-). \n=> %d\n",
        $inactive_number;
    $buff = $buff . sprintf "WIthdraw: Withdrawn within 30 days (-). \n=> %d\n", $withdraw_number;
    $buff = $buff . sprintf "Forced to black List (-) \n=> %d\n", $delete_number;
    $buff
        = $buff
        . sprintf
        "Late Active: Those who had registered 31 days ago but earned some points within 30 days (+) \n=> %d\n",
        $late_active_number;
    $buff = $buff . sprintf "6AU EOM: 6AU End of Month. \n=> %d\n", $number_6au_eom->reward_number;
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "recent 30 day active ratio \n";
    $buff = $buff . sprintf "-------------------------- \n";

    foreach (@recent_30_day_active_ratio) {
        my $active_ratio = $_;
        $buff = $buff . sprintf "%s -> %s\n=> %8d/%8d (%s) \n",
            $active_ratio->start_register_date->strftime('%Y-%m-%d'),
            $active_ratio->end_register_date->strftime('%Y-%m-%d'),
            $active_ratio->reward_number,
            $active_ratio->register_number,
            $active_ratio->active_ratio;
    }
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "-------------------------- \n";
    $buff = $buff . sprintf "recent daily active ratio  \n";
    $buff = $buff . sprintf "-------------------------- \n";
    foreach (@recent_daily_active_ratio) {
        my $active_ratio = $_;
        $buff = $buff . sprintf "%s -> %s\n=> %8d/%8d (%s) \n",
            $active_ratio->start_register_date->strftime('%Y-%m-%d'),
            $active_ratio->end_register_date->strftime('%Y-%m-%d'),
            $active_ratio->reward_number,
            $active_ratio->register_number,
            $active_ratio->active_ratio;
    }
    $buff = $buff . sprintf "\n";

    return $buff;
}

##
#   1. Get the data of 30 day active ratio
#   2. Calculate the ratio and put into the container
##
sub get_recent_30_day_active_ratio {
    my $self               = shift;
    my @array_active_ratio = $self->prepare_container_recent_30_day_active_ratio();
    foreach (@array_active_ratio) {
        my $active_ratio  = $_;
        my $reward_number = Wenwen::Model::Service::PanelKPI->count_active_number(
            $handle,
            $active_ratio->start_register_date,
            $active_ratio->end_register_date,
            $active_ratio->start_reward_date,
            $active_ratio->end_reward_date
        );
        my $register_number = Wenwen::Model::Service::PanelKPI->count_register_number(
            $handle,
            $active_ratio->start_register_date,
            $active_ratio->end_register_date
        );
        $active_ratio->reward_number($reward_number);
        $active_ratio->register_number($register_number);
        $active_ratio->cal_active_ratio();
    }
    return @array_active_ratio;
}

##
#   1. Get the data of 6AU EOM (6AU on base_date)
##
sub get_numbers_of_6au_eom {
    my $self = shift;

    my $number_6au_eom = $self->prepare_container_6au_eom();

    my $reward_number = Wenwen::Model::Service::PanelKPI->count_active_number(
        $handle,
        $number_6au_eom->start_register_date,
        $number_6au_eom->end_register_date,
        $number_6au_eom->start_reward_date,
        $number_6au_eom->end_reward_date
    );

    $number_6au_eom->reward_number($reward_number);
    return $number_6au_eom;
}

##
#   1. Get the data of 6AU BOM (6AU 31 days before base_date)
##
sub get_numbers_of_6au_bom {
    my $self = shift;

    my $number_6au_bom = $self->prepare_container_6au_bom();

    my $reward_number = Wenwen::Model::Service::PanelKPI->count_active_number(
        $handle,
        $number_6au_bom->start_register_date,
        $number_6au_bom->end_register_date,
        $number_6au_bom->start_reward_date,
        $number_6au_bom->end_reward_date
    );

    $number_6au_bom->reward_number($reward_number);
    return $number_6au_bom;
}

##
#   New: Newly registered users (including not active) within 30 days (+)
#   1. Get the data of newly registered user (in 30 days from base_date)
##
sub get_newly_registered_user {
    my $self                  = shift;
    my $newly_registered_user = $self->prepare_container_newly_registered_user();

    my $registered_user = Wenwen::Model::Service::PanelKPI->count_register_number(
        $handle,
        $newly_registered_user->start_register_date,
        $newly_registered_user->end_register_date
    );
    $newly_registered_user->register_number($registered_user);

    return $newly_registered_user;
}

##
#   Inactive: Inactiated in recent 30 days (-)
#   who active in 200 - 180 days ago never took a point in 30 - 0 days ago
##
sub get_inactivated_in_recent_30_day {
    my $self = shift;

    my $inactive_to   = $self->base_date;
    my $inactive_from = $inactive_to - 30 * ONE_DAY;
    my $active_to     = $inactive_from - 150 * ONE_DAY;
    my $active_from   = $active_to - 30 * ONE_DAY;

    my $registered_number
        = Wenwen::Model::Service::PanelKPI->count_recent_30_day_inactivated($handle, $active_from,
        $active_to, $inactive_from, $inactive_to);
    return $registered_number;
}

##
#   Dead Dog: Newly registered users who have NOT earned any activation points (-)
#   Newly registered users who have NOT earned any active points.
##
sub get_dead_dogs {
    my $self = shift;

    my $register_to   = $self->base_date;
    my $register_from = $register_to - 30 * ONE_DAY;
    my $active_to     = $register_to;
    my $active_from   = $register_from;
    my $inactive_number
        = Wenwen::Model::Service::PanelKPI->count_inactive_register($handle, $register_from,
        $register_to, $active_from, $active_to);

    return $inactive_number;
}

##
#   WIthdraw: Withdrawn within 30 days (-)
#   Those who has withdrawn within 30 days.
##
sub get_withdraw_in_30_days {
    my $self = shift;

    my $withdraw_to   = $self->base_date;
    my $withdraw_from = $withdraw_to - 30 * ONE_DAY;
    my $withdraw_number
        = Wenwen::Model::Service::PanelKPI->count_withdraw($handle, $withdraw_from, $withdraw_to);

    return $withdraw_number;
}

##
#   Forced to black List (-)
#   Those who has been forced to add Black List.
##
sub get_blacklist_in_30_days {
    my $self = shift;

    my $delete_to   = $self->base_date;
    my $delete_from = $delete_to - 30 * ONE_DAY;
    my $delete_number
        = Wenwen::Model::Service::PanelKPI->count_blacklist($handle, $delete_from, $delete_to);

    return $delete_number;
}

##
#   Those who had registered 31 days ago but earned some points within 30 days.
##
sub get_late_active_in_30_days {
    my $self = shift;

    my $active_to   = $self->base_date;
    my $active_from = $active_to - 30 * ONE_DAY;

    my $register_to = $self - 31 * ONE_DAY;
    my $register_from = Time::Piece->strptime('2000-01-01', '%Y-%m-%d');

    my $late_active_number = Wenwen::Model::Service::PanelKPI->count_late_active(
        $handle,
        $active_from,
        $active_to,
        $register_from,
        $register_to

    );

    return $late_active_number;
}

##
#   1. Get the data of daily active ratio for recent 7 days
#   2. Calculate the ratio and put into the container
##
sub get_recent_daily_active_ratio {
    my $self               = shift;
    my @array_active_ratio = $self->prepare_container_daily_active_ratio();
    foreach (@array_active_ratio) {
        my $active_ratio  = $_;
        my $reward_number = Wenwen::Model::Service::PanelKPI->count_active_number(
            $handle,
            $active_ratio->start_register_date,
            $active_ratio->end_register_date,
            $active_ratio->start_reward_date,
            $active_ratio->end_reward_date
        );
        my $register_number = Wenwen::Model::Service::PanelKPI->count_register_number(
            $handle,
            $active_ratio->start_register_date,
            $active_ratio->end_register_date
        );
        $active_ratio->reward_number($reward_number);
        $active_ratio->register_number($register_number);
        $active_ratio->cal_active_ratio();
    }
    return @array_active_ratio;
}

##
#   Create the container for 30 day active ratio
#   7 periods with each 30 days
#   The start register date will be 2000/1/1 for the last period
##
sub prepare_container_recent_30_day_active_ratio {
    my $self = shift;

    my @array_day_active_ratio = ();

    my $duration_days = 30;

    # 7 period of 30 day active ratio (counter: 0-6)
    my $total_period = 6;
    my $counter      = 0;

    my $base_end_date   = $self->base_date;
    my $base_start_date = $base_end_date - $duration_days * ONE_DAY;

    my $end_reward_date   = $base_end_date;
    my $start_reward_date = $base_start_date;

    my $end_register_date   = $base_end_date;
    my $start_register_date = $base_start_date;

    while ($counter <= $total_period) {
        my $active_ratio = Wenwen::Task::ActiveRatio->new(
            start_register_date => $start_register_date,
            end_register_date   => $end_register_date,
            start_reward_date   => $start_reward_date,
            end_reward_date     => $end_reward_date,
        );
        push(@array_day_active_ratio, $active_ratio);
        $counter++;
        $end_register_date -= $duration_days * ONE_DAY;
        if ($counter == $total_period) {
            $start_register_date = Time::Piece->strptime('2000-01-01', '%Y-%m-%d');
        }
        else {
            $start_register_date -= $duration_days * ONE_DAY;
        }

    }
    return @array_day_active_ratio;

}

##
#   Prepare the container for 6au eom
##
sub prepare_container_6au_eom {
    my $self = shift;

    my $end_reward_date     = $self->base_date;
    my $end_register_date   = $self->base_date;
    my $start_reward_date   = $end_reward_date - 180 * ONE_DAY;
    my $start_register_date = Time::Piece->strptime('2000-01-01', '%Y-%m-%d');
    my $number_6au_eom      = Wenwen::Task::ActiveRatio->new(
        start_register_date => $start_register_date,
        end_register_date   => $end_register_date,
        start_reward_date   => $start_reward_date,
        end_reward_date     => $end_reward_date,
    );
    return $number_6au_eom;
}

##
#   Prepare the container for 6au bom (6au 31 days before base_date)
##
sub prepare_container_6au_bom {
    my $self = shift;

    my $end_reward_date     = $self->base_date - 31 * ONE_DAY;
    my $end_register_date   = $end_reward_date;
    my $start_reward_date   = $end_reward_date - 180 * ONE_DAY;
    my $start_register_date = Time::Piece->strptime('2000-01-01', '%Y-%m-%d');
    my $number_6au_bom      = Wenwen::Task::ActiveRatio->new(
        start_register_date => $start_register_date,
        end_register_date   => $end_register_date,
        start_reward_date   => $start_reward_date,
        end_reward_date     => $end_reward_date,
    );
    return $number_6au_bom;
}

##
#   Prepare the container for newly registered user (users registered in 30 days from base_date)
##
sub prepare_container_newly_registered_user {
    my $self = shift;

    my $end_register_date     = $self->base_date;
    my $start_register_date   = $end_register_date - 30 * ONE_DAY;
    my $newly_registered_user = Wenwen::Task::ActiveRatio->new(
        start_register_date => $start_register_date,
        end_register_date   => $end_register_date,
    );
    return $newly_registered_user;
}

##
#   Create the container for 30 day active ratio
#   7 periods with each 30 days
#   The start register date will be 2000/1/1 for the last period
##
sub prepare_container_daily_active_ratio {
    my $self = shift;

    my @array_day_active_ratio = ();

    my $duration_days = 1;

    # 7 period of 30 day active ratio (counter: 0-6)
    my $total_period = 6;
    my $counter      = 0;

    my $base_end_date   = $self->base_date;
    my $base_start_date = $base_end_date - $duration_days * ONE_DAY;

    my $end_reward_date   = $base_end_date;
    my $start_reward_date = $base_start_date;

    my $end_register_date   = $base_end_date;
    my $start_register_date = $base_start_date;

    while ($counter <= $total_period) {
        my $active_ratio = Wenwen::Task::ActiveRatio->new(
            start_register_date => $start_register_date,
            end_register_date   => $end_register_date,
            start_reward_date   => $start_reward_date,
            end_reward_date     => $end_reward_date,
        );
        push(@array_day_active_ratio, $active_ratio);
        $end_register_date   -= $duration_days * ONE_DAY;
        $start_register_date -= $duration_days * ONE_DAY;
        $counter++;
    }
    return @array_day_active_ratio;

}

1;
