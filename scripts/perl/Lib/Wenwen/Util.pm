package Wenwen::Util;
use common::sense;

use Exporter qw(import);
use File::Basename;

our @EXPORT_OK = qw(
    home
    path_to
);
our %EXPORT_TAGS = ();

sub home {
    File::Spec->rel2abs(File::Spec->catdir(File::Basename::dirname(__FILE__), '..', '..'),);
}

sub path_to {
    my $path = shift;
    home() . '/' . $path;
}

1;
