package Wenwen::Util;
use common::sense;

use Exporter qw(import);
use File::Basename;
use File::Spec;
use Time::Piece ();

our @EXPORT_OK = qw(
    deflate_timestamp
    home
    inflate_timestamp
    path_to
);

sub home {
    File::Spec->rel2abs(File::Spec->catdir(File::Basename::dirname(__FILE__), '..', '..'),);
}

sub path_to {
    my $path = shift;
    home() . '/' . $path;
}

sub inflate_timestamp {
    my $val = shift;
    $val
        ? eval { Time::Piece->localtime(Time::Piece->strptime($val, '%Y-%m-%d %T')); }
        || undef
        : undef;
}

sub deflate_timestamp {
    my $val = shift;
          ref $val eq 'SCALAR' ? $val
        : ref $val             ? $val->strftime('%F %T')
        :                        $val;
}

1;
