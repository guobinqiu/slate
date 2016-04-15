#!/usr/bin/env perl

use common::sense;
use FindBin qw($Bin);
use lib "$Bin/logic";
use lib "$Bin/../Lib";

use logic::GetPerformanceReport;
use logic::Email;

my $logic = logic::GetPerformanceReport->new();

my $buff = "\n";

my $number_6au_bom             = $logic->get_numbers_of_6au_bom();
my $registered_number          = $logic->get_inactivated_in_recent_30_day();
my $newly_registered_user      = $logic->get_newly_registered_user();
my $inactive_number            = $logic->get_dead_dogs();
my $withdraw_number            = $logic->get_withdraw_in_30_days();
my $delete_number              = $logic->get_blacklist_in_30_days();
my $late_active_number         = $logic->get_late_active_in_30_days();
my $number_6au_eom             = $logic->get_numbers_of_6au_eom();
my @recent_30_day_active_ratio = $logic->get_recent_30_day_active_ratio();
my @recent_daily_active_ratio  = $logic->get_recent_daily_active_ratio();

$buff = $buff . sprintf "-------------------------- \n";
$buff = $buff . sprintf "KPI \n";
$buff = $buff . sprintf "-------------------------- \n";
$buff = $buff . sprintf "6AU BOM: Number of 6AU as of 31 days ago (Base). \n=> %d\n",
    $number_6au_bom->reward_number;
$buff = $buff . sprintf "Inactive: Inactiated in recent 30 days (-). \n=> %d\n", $registered_number;
$buff
    = $buff
    . sprintf "New: Newly registered users (including not active) within 30 days (+). \n=> %d\n",
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
    $buff = $buff . sprintf "%s -> %s %8d/%8d (%s) \n",
        $active_ratio->start_register_date,
        $active_ratio->end_register_date,
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
    $buff = $buff . sprintf "%s -> %s %8d/%8d (%s) \n",
        $active_ratio->start_register_date,
        $active_ratio->end_register_date,
        $active_ratio->reward_number,
        $active_ratio->register_number,
        $active_ratio->active_ratio;
}
$buff = $buff . sprintf "\n";

my $from    = 'ds-sys-china@d8aspring.com';
my $to      = 'xiaoyi.chai@d8aspring.com';
my $subject = 'Performance Report ' . $logic->base_date;
my $body    = $buff;

my $sender = logic::Email->new();
$sender->send($to, $subject, $body);

=begin comment

=end comment
