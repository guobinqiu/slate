use common::sense;

use Getopt::Long;
use Wenwen::Email;
use Wenwen::Task::GetPerformanceReport;

my %opt = (base_date => Time::Piece->localtime->strftime('%Y-%m-%d'),);

GetOptions('base_date=s' => \$opt{base_date},);

die "Usage: $0 [--base_date=yyyy-mm-dd]"
    unless !defined($opt{base_date})
    or $opt{base_date} =~ /^\d{4}-\d{2}-\d{2}$/;

my $logic = Wenwen::Task::GetPerformanceReport->new(
    base_date => Time::Piece->strptime($opt{base_date}, '%Y-%m-%d'));

my $to      = 'rpa-sys-china@d8aspring.com,ds-Product-china@d8aspring.com';
my $subject = 'Performance Report ' . $logic->base_date->strftime('%Y-%m-%d');
my $body    = $logic->do_task();

my $sender = Wenwen::Email->new();
$sender->send($to, $subject, $body) or die "";

