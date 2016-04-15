package logic::GetPerformanceReport;

use strict;
use warnings;
use v5.22;

#use diagnostics -verbose;

use Moo;
use DateTime;

use FindBin qw($Bin);
use lib "$Bin/logic";
use lib "$Bin/../Lib";

use logic::ActiveRatio;
use Wenwen::Model;
use Wenwen::Model::Service::PanelKPI;

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

    if (defined($base_date) && ref $base_date eq 'DateTime') {
        return $base_date;
    }
    else {

        my $start_of_today = DateTime->now();
        $start_of_today->set(hour   => 0);
        $start_of_today->set(minute => 0);
        $start_of_today->set(second => 0);
        return $start_of_today;
    }
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
    my $inactive_from = $inactive_to->clone()->add(days => -30);
    my $active_to     = $inactive_from->clone()->add(days => -150);
    my $active_from   = $active_to->clone()->add(days => -30);

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
    my $register_from = $register_to->clone()->add(days => -30);
    my $active_to     = $register_to->clone();
    my $active_from   = $register_from->clone();
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

    my $withdraw_to = $self->base_date;
    my $withdraw_from = $withdraw_to->clone()->add(days => -30);
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

    my $delete_to = $self->base_date;
    my $delete_from = $delete_to->clone()->add(days => -30);
    my $delete_number
        = Wenwen::Model::Service::PanelKPI->count_blacklist($handle, $delete_from, $delete_to);

    return $delete_number;
}

##
#   Those who had registered 31 days ago but earned some points within 30 days.
##
sub get_late_active_in_30_days {
    my $self = shift;

    my $active_to = $self->base_date;
    my $active_from = $active_to->clone()->add(days => -30);

    my $register_to = $self->base_date->clone()->add(days => -31);
    my $register_from = $register_to->clone()->set(year => 2000, month => 1, day => 1);

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

    my $base_end_date = $self->base_date->clone();
    my $base_start_date = $base_end_date->clone()->add(days => -$duration_days);

    my $end_reward_date   = $base_end_date->clone();
    my $start_reward_date = $base_start_date->clone();

    my $end_register_date   = $base_end_date->clone();
    my $start_register_date = $base_start_date->clone();

    while ($counter <= $total_period) {
        my $active_ratio = logic::ActiveRatio->new(
            start_register_date => $start_register_date->clone(),
            end_register_date   => $end_register_date->clone(),
            start_reward_date   => $start_reward_date->clone(),
            end_reward_date     => $end_reward_date->clone(),
        );
        push(@array_day_active_ratio, $active_ratio);
        if ($counter == $total_period) {
            $end_register_date->add(days => -$duration_days);
            $start_register_date->set(year  => 2000);
            $start_register_date->set(month => 1);
            $start_register_date->set(day   => 1);
        }
        else {
            $end_register_date->add(days => -$duration_days);
            $start_register_date->add(days => -$duration_days);
        }
        $counter++;
    }
    return @array_day_active_ratio;

}

##
#   Prepare the container for 6au eom
##
sub prepare_container_6au_eom {
    my $self = shift;

    my $end_reward_date     = $self->base_date->clone();
    my $end_register_date   = $self->base_date->clone();
    my $start_reward_date   = $end_reward_date->clone()->add(days => -180);
    my $start_register_date = $end_register_date->clone()->set(year => 2000, month => 1, day => 1);
    my $number_6au_eom      = logic::ActiveRatio->new(
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

    my $end_reward_date     = $self->base_date->clone()->add(days => -31);
    my $end_register_date   = $end_reward_date->clone();
    my $start_reward_date   = $end_reward_date->clone()->add(days => -180);
    my $start_register_date = $end_register_date->clone()->set(year => 2000, month => 1, day => 1);
    my $number_6au_bom      = logic::ActiveRatio->new(
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

    my $end_register_date     = $self->base_date->clone();
    my $start_register_date   = $end_register_date->clone()->add(days => -30);
    my $newly_registered_user = logic::ActiveRatio->new(
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

    my $base_end_date = $self->base_date->clone();
    my $base_start_date = $base_end_date->clone()->add(days => -$duration_days);

    my $end_reward_date   = $base_end_date->clone();
    my $start_reward_date = $base_start_date->clone();

    my $end_register_date   = $base_end_date->clone();
    my $start_register_date = $base_start_date->clone();

    while ($counter <= $total_period) {
        my $active_ratio = logic::ActiveRatio->new(
            start_register_date => $start_register_date->clone(),
            end_register_date   => $end_register_date->clone(),
            start_reward_date   => $start_reward_date->clone(),
            end_reward_date     => $end_reward_date->clone(),
        );
        push(@array_day_active_ratio, $active_ratio);
        $end_register_date->add(days => -$duration_days);
        $start_register_date->add(days => -$duration_days);
        $counter++;
    }
    return @array_day_active_ratio;

}

1;
