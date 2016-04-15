#!/usr/bin/env perl
use strict;
use warnings;
use v5.10;
use FindBin qw($Bin);
use lib "$Bin/logic";
use lib "$Bin/../Lib";

use logic::LogicGetPerformanceReport;
use logic::Email;


my $logic = logic::LogicGetPerformanceReport->new();

my @recent_30_day_active_ratio = $logic->get_recent_30_day_active_ratio();

print "-------------------------- \n";
print "recent 30 day active ratio \n";
print "-------------------------- \n";
foreach (@recent_30_day_active_ratio) {
    my $active_ratio = $_;
    print $active_ratio->start_register_date, "->", $active_ratio->end_register_date, "\t";
    print $active_ratio->reward_number, "/";
    print $active_ratio->register_number, "\t";
    print $active_ratio->active_ratio;
    print "\n";
}
print "\n";

print "-------------------------- \n";
print "KPI \n";
print "-------------------------- \n";
my $number_6au_bom = $logic->get_numbers_of_6au_bom();
print "Number of 6AU BOM = ", $number_6au_bom->reward_number;
print "\n";

my $number_6au_eom = $logic->get_numbers_of_6au_eom();
print "Number of 6AU EOM = ", $number_6au_eom->reward_number;
print "\n";

my $newly_registered_user = $logic->get_newly_registered_user();
print "Total newly registered users (including not active) within 30 days. = ", $newly_registered_user->register_number;
print "\n";

my $registered_number = $logic->get_inactivated_in_recent_30_day();
print "Recent 30-day INACTIVATED count. = ", $registered_number;
print "\n";

my $inactive_number = $logic->get_dead_dogs();
print "Newly registered users who have NOT earned any active points. = ", $inactive_number;
print "\n";




=begin comment
my $number_6au_eom = $logic->numbers_of_6au_eom();

my $number_6au_bom = $logic->numbers_of_6au_bom();

my $newly_registered_user = $logic->newly_registered_user();

my $from = 'ds-sys-china@d8aspring.com';
my $to = 'xiaoyi.chai@d8aspring.com';
my $subject = 'Performance Report';
my $body = '';

$body = $body.$recent_30_day_active_ratio.$number_6au_eom.$number_6au_bom.$newly_registered_user;

print $body;

my $sender = logic::Email->new();
$sender->send($to, $subject, $body);


=end comment
