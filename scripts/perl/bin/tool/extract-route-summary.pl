use common::sense;

use Getopt::Long;
use Encode qw(encode_utf8);
use Text::CSV_XS;
use Time::Piece ();
use Time::Seconds;
use Wenwen::Model::UserSignUpRoute;
use Wenwen::Model;

my %opt = (
    from => (Time::Piece->localtime - ONE_DAY)->strftime('%F'),
    to   => Time::Piece->localtime->strftime('%F'),
);
GetOptions(
    'date-from=s' => \$opt{from},
    'date-to=s'   => \$opt{to},
);

die "Usage: $0 [--date-from=yyyy-mm-dd] [--date-to=yyyy-mm-dd]"
    unless $opt{from} =~ /\d{4}-\d{2}-\d{2}/
    and $opt{to} =~ /\d{4}-\d{2}-\d{2}/;

my $handle = Wenwen::Model->create_handle;
my $csv    = Text::CSV_XS->new({ binary => 1, always_quote => 1 });
my @header = qw(register_complete_date source_route count);
my $res    = Wenwen::Model::UserSignUpRoute->retrieve_route_summary(
    $handle,
    {   'user.register_complete_date_from' => $opt{from},
        'user.register_complete_date_to'   => $opt{to},
    }
);

$csv->say(*STDOUT, \@header);

for my $row (@$res) {
    $csv->say(*STDOUT, [ map { encode_utf8($row->{$_}) } @header ]);
}

1;
