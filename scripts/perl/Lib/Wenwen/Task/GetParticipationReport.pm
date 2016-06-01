package Wenwen::Task::GetParticipationReport;

use common::sense;

use Moo;
use Time::Piece ();
use Time::Piece::Plus;
use Time::Seconds;
use Text::CSV_XS;

use Wenwen::Model;
use Wenwen::Model::Service::ParticipationHistory;

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

    my $sop_result_ref     = $self->get_sop_participation_history();
    my $cint_result_ref    = $self->get_cint_participation_history();
    my $fulcrum_result_ref = $self->get_fulcrum_participation_history();
    my $ssi_result_ref     = $self->get_ssi_participation_history();

    $buff = $buff . sprintf "Points Report from %s to %s \n", $self->from_date->strftime('%F %T'),
        $self->to_date->strftime('%F %T');
    $buff = $buff . sprintf "\n";

    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "SOP Participation History";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "yyyymm, app_project_id, point_type, point";
    $buff = $buff . sprintf "\n";
    foreach my $row (@$sop_result_ref) {
        $buff = $buff . sprintf join(", ", @$row);
        $buff = $buff . sprintf "\n";
    }
    $buff = $buff . sprintf "\n";

    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "Cint Participation History";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "yyyymm, API Type, Project ID, point";
    $buff = $buff . sprintf "\n";
    foreach my $row (@$cint_result_ref) {
        $buff = $buff . sprintf join(", ", @$row);
        $buff = $buff . sprintf "\n";
    }
    $buff = $buff . sprintf "\n";

    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "Fulcrum Participation History";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "yyyymm, API Type, Project ID, point";
    $buff = $buff . sprintf "\n";
    foreach my $row (@$fulcrum_result_ref) {
        $buff = $buff . sprintf join(", ", @$row);
        $buff = $buff . sprintf "\n";
    }
    $buff = $buff . sprintf "\n";

    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "SSI Participation History";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "--------------------------";
    $buff = $buff . sprintf "\n";
    $buff = $buff . sprintf "yyyymm, API Type, Project ID, point";
    $buff = $buff . sprintf "\n";
    foreach my $row (@$ssi_result_ref) {
        $buff = $buff . sprintf join(", ", @$row);
        $buff = $buff . sprintf "\n";
    }
    $buff = $buff . sprintf "\n";

    $buff = $buff . sprintf "\n";

    return $buff;
}

##
#   SOP Participation history
##
sub get_sop_participation_history {
    my $self = shift;

    my $result
        = Wenwen::Model::Service::ParticipationHistory->select_sop_participation_history($handle,
        $self->from_date, $self->to_date);
    return $result;
}

##
#   Cint Participation history
##
sub get_cint_participation_history {
    my $self = shift;

    my $result
        = Wenwen::Model::Service::ParticipationHistory->select_cint_participation_history($handle,
        $self->from_date, $self->to_date);
    return $result;
}

##
#   Fulcrum Participation history
##
sub get_fulcrum_participation_history {
    my $self = shift;

    my $result
        = Wenwen::Model::Service::ParticipationHistory->select_fulcrum_participation_history(
        $handle, $self->from_date, $self->to_date);
    return $result;
}

##
#   SSI Participation history
##
sub get_ssi_participation_history {
    my $self = shift;

    my $result
        = Wenwen::Model::Service::ParticipationHistory->select_ssi_participation_history($handle,
        $self->from_date, $self->to_date);
    return $result;
}

1;
