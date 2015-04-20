#!/usr/bin/perl
use strict;
use warnings;

use Jili::DBConnection;
use Uri::Parser;
use Yiqifa::CpsConfirmed;

use Data::Dumper;

sub get_confirmed_hashref {
    my ($files) = @_;

}

my $confirmed_data_dir = './status_recallback';
my $files = Yiqifa::CpsConfirmed::get_confirmed_utf8_filelist($confirmed_data_dir);

print Dumper($files);

__END__

1. load cps confirmed data

2. filter out the confirmed entry

3. build the sql for querying.
4. query the order&task history. 
