package logic::ActiveRatio;

use strict;
use warnings;
use v5.22;

#use diagnostics -verbose;

use Moo;
use DateTime;

has end_register_date => (is => 'ro',);

has start_register_date => (is => 'ro',);

has end_reward_date => (is => 'ro',);

has start_reward_date => (is => 'ro',);

has reward_number => (is => 'rw',);

has register_number => (is => 'rw',);

has active_ratio => (is => 'rw',);

has to_string => (is => 'rw',);

sub cal_active_ratio {
    my $self = shift;
    if ($self->register_number != 0) {
        $self->active_ratio(flt_to_pct($self->reward_number / $self->register_number));
    }
    else {
        $self->active_ratio(flt_to_pct(0));
    }
}

## float to percentage
sub flt_to_pct {
    return sprintf("%.4f", shift) * 100 . '%';
}

1;
