# This file is auto-generated by the Perl DateTime Suite time zone
# code generator (0.07) This code generator comes with the
# DateTime::TimeZone module distribution in the tools/ directory

#
# Generated from /tmp/4wpj_fAzbR/australasia.  Olson data version 2016c
#
# Do not edit this file directly.
#
package DateTime::TimeZone::Pacific::Fakaofo;
$DateTime::TimeZone::Pacific::Fakaofo::VERSION = '1.97';
use strict;

use Class::Singleton 1.03;
use DateTime::TimeZone;
use DateTime::TimeZone::OlsonDB;

@DateTime::TimeZone::Pacific::Fakaofo::ISA = ( 'Class::Singleton', 'DateTime::TimeZone' );

my $spans =
[
    [
DateTime::TimeZone::NEG_INFINITY, #    utc_start
59958271496, #      utc_end 1901-01-01 11:24:56 (Tue)
DateTime::TimeZone::NEG_INFINITY, #  local_start
59958230400, #    local_end 1901-01-01 00:00:00 (Tue)
-41096,
0,
'LMT',
    ],
    [
59958271496, #    utc_start 1901-01-01 11:24:56 (Tue)
63460926000, #      utc_end 2011-12-30 11:00:00 (Fri)
59958231896, #  local_start 1901-01-01 00:24:56 (Tue)
63460886400, #    local_end 2011-12-30 00:00:00 (Fri)
-39600,
0,
'TKT',
    ],
    [
63460926000, #    utc_start 2011-12-30 11:00:00 (Fri)
DateTime::TimeZone::INFINITY, #      utc_end
63460972800, #  local_start 2011-12-31 00:00:00 (Sat)
DateTime::TimeZone::INFINITY, #    local_end
46800,
0,
'TKT',
    ],
];

sub olson_version {'2016c'}

sub has_dst_changes {0}

sub _max_year {2026}

sub _new_instance {
    return shift->_init( @_, spans => $spans );
}



1;

