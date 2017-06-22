use common::sense;

use DateTime;
use Wenwen::Model;
use Wenwen::Task::CleanupEmailJobTask;
use Getopt::Long;

my %opt = (
    delete_before => get_delete_before(),
);

GetOptions(
    'before=s' => \$opt{delete_before},
);

die "Usage: $0 [--before=yyyy-mm-dd]"
    unless $opt{delete_before} =~ /\d{4}-\d{2}-\d{2}/;

my $handle = Wenwen::Model->create_handle;

my $task = Wenwen::Task::CleanupEmailJobTask->new(handle => $handle);
$task->delete_finished_before_date($opt{delete_before});

sub get_delete_before {
    my $t = DateTime->today;

    DateTime->new(
        year  => $t->year,
        month => $t->month,
        day => $t->day
    )->ymd;
}

1;
