use common::sense;

use DateTime;
use Wenwen::Model;
use Wenwen::Task::CleanupEmailJobTask;
use Getopt::Long;

my $first_day_of_this_month = get_first_day_of_this_month();

GetOptions(
    'before=s' => \$first_day_of_this_month,
);

die "Usage: $0 [--before=yyyy-mm-dd]"
    unless $first_day_of_this_month =~ /\d{4}-\d{2}-\d{2}/;

#print $first_day_of_this_month;

my $handle = Wenwen::Model->create_handle;

my $task = Wenwen::Task::CleanupEmailJobTask->new(handle => $handle);
$task->delete_finished_by_date($first_day_of_this_month);

sub get_first_day_of_this_month {
    my $t = DateTime->today;

    DateTime->new(
        year  => $t->year,
        month => $t->month,
        day => 1
    )->ymd;
}

1;
