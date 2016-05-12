use common::sense;

use Getopt::Long;
use Wenwen::Email;
use Wenwen::Task::GetPointsReport;

use Time::Piece ();
use Time::Piece::Plus;
use Wenwen::Util;

my %opt = (base_date => Time::Piece->localtime->strftime('%Y-%m-01'),);

GetOptions('base_date=s' => \$opt{base_date},);

die "Usage: $0 [--base_date=yyyy-mm-dd]"
    unless !defined($opt{base_date})
    or $opt{base_date} =~ /^\d{4}-\d{2}-\d{2}$/;

my $logic = Wenwen::Task::GetPointsReport->new(
    base_date => Time::Piece->strptime($opt{base_date}, '%Y-%m-%d'));

my $to      = 'xiaoyi.chai@d8aspring.com';
my $subject = 'Points Report ' . $logic->base_date->strftime('%Y-%m-%d');
my $body    = $logic->do_task();

my $sender = Wenwen::Email->new();
$sender->send($to, $subject, $body) or die "";

