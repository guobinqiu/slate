use common::sense;

use DateTime;
use Wenwen::Model;
use Wenwen::Task::CleanupEmailJobTask;
use Getopt::Long;

my %opt = (
    first_day_of_this_month => get_first_day_of_this_month(),
);

GetOptions(
    'before=s' => \$opt{first_day_of_this_month},
);

die "Usage: $0 [--before=yyyy-mm-dd]"
    unless $opt{first_day_of_this_month} =~ /\d{4}-\d{2}-\d{2}/;

my $handle = Wenwen::Model->create_handle;

my $task = Wenwen::Task::CleanupEmailJobTask->new(handle => $handle);
$task->delete_finished_before_date($opt{first_day_of_this_month});

sub get_first_day_of_this_month {
    my $t = DateTime->today;

    DateTime->new(
        year  => $t->year,
        month => $t->month,
        day => 1
    )->ymd;
}

1;
