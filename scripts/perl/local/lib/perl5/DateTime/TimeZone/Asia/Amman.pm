# This file is auto-generated by the Perl DateTime Suite time zone
# code generator (0.07) This code generator comes with the
# DateTime::TimeZone module distribution in the tools/ directory

#
# Generated from /tmp/4wpj_fAzbR/asia.  Olson data version 2016c
#
# Do not edit this file directly.
#
package DateTime::TimeZone::Asia::Amman;
$DateTime::TimeZone::Asia::Amman::VERSION = '1.97';
use strict;

use Class::Singleton 1.03;
use DateTime::TimeZone;
use DateTime::TimeZone::OlsonDB;

@DateTime::TimeZone::Asia::Amman::ISA = ( 'Class::Singleton', 'DateTime::TimeZone' );

my $spans =
[
    [
DateTime::TimeZone::NEG_INFINITY, #    utc_start
60904906576, #      utc_end 1930-12-31 21:36:16 (Wed)
DateTime::TimeZone::NEG_INFINITY, #  local_start
60904915200, #    local_end 1931-01-01 00:00:00 (Thu)
8624,
0,
'LMT',
    ],
    [
60904906576, #    utc_start 1930-12-31 21:36:16 (Wed)
62243848800, #      utc_end 1973-06-05 22:00:00 (Tue)
60904913776, #  local_start 1930-12-31 23:36:16 (Wed)
62243856000, #    local_end 1973-06-06 00:00:00 (Wed)
7200,
0,
'EET',
    ],
    [
62243848800, #    utc_start 1973-06-05 22:00:00 (Tue)
62253954000, #      utc_end 1973-09-30 21:00:00 (Sun)
62243859600, #  local_start 1973-06-06 01:00:00 (Wed)
62253964800, #    local_end 1973-10-01 00:00:00 (Mon)
10800,
1,
'EEST',
    ],
    [
62253954000, #    utc_start 1973-09-30 21:00:00 (Sun)
62272274400, #      utc_end 1974-04-30 22:00:00 (Tue)
62253961200, #  local_start 1973-09-30 23:00:00 (Sun)
62272281600, #    local_end 1974-05-01 00:00:00 (Wed)
7200,
0,
'EET',
    ],
    [
62272274400, #    utc_start 1974-04-30 22:00:00 (Tue)
62285490000, #      utc_end 1974-09-30 21:00:00 (Mon)
62272285200, #  local_start 1974-05-01 01:00:00 (Wed)
62285500800, #    local_end 1974-10-01 00:00:00 (Tue)
10800,
1,
'EEST',
    ],
    [
62285490000, #    utc_start 1974-09-30 21:00:00 (Mon)
62303810400, #      utc_end 1975-04-30 22:00:00 (Wed)
62285497200, #  local_start 1974-09-30 23:00:00 (Mon)
62303817600, #    local_end 1975-05-01 00:00:00 (Thu)
7200,
0,
'EET',
    ],
    [
62303810400, #    utc_start 1975-04-30 22:00:00 (Wed)
62317026000, #      utc_end 1975-09-30 21:00:00 (Tue)
62303821200, #  local_start 1975-05-01 01:00:00 (Thu)
62317036800, #    local_end 1975-10-01 00:00:00 (Wed)
10800,
1,
'EEST',
    ],
    [
62317026000, #    utc_start 1975-09-30 21:00:00 (Tue)
62335432800, #      utc_end 1976-04-30 22:00:00 (Fri)
62317033200, #  local_start 1975-09-30 23:00:00 (Tue)
62335440000, #    local_end 1976-05-01 00:00:00 (Sat)
7200,
0,
'EET',
    ],
    [
62335432800, #    utc_start 1976-04-30 22:00:00 (Fri)
62351326800, #      utc_end 1976-10-31 21:00:00 (Sun)
62335443600, #  local_start 1976-05-01 01:00:00 (Sat)
62351337600, #    local_end 1976-11-01 00:00:00 (Mon)
10800,
1,
'EEST',
    ],
    [
62351326800, #    utc_start 1976-10-31 21:00:00 (Sun)
62366968800, #      utc_end 1977-04-30 22:00:00 (Sat)
62351334000, #  local_start 1976-10-31 23:00:00 (Sun)
62366976000, #    local_end 1977-05-01 00:00:00 (Sun)
7200,
0,
'EET',
    ],
    [
62366968800, #    utc_start 1977-04-30 22:00:00 (Sat)
62380184400, #      utc_end 1977-09-30 21:00:00 (Fri)
62366979600, #  local_start 1977-05-01 01:00:00 (Sun)
62380195200, #    local_end 1977-10-01 00:00:00 (Sat)
10800,
1,
'EEST',
    ],
    [
62380184400, #    utc_start 1977-09-30 21:00:00 (Fri)
62398418400, #      utc_end 1978-04-29 22:00:00 (Sat)
62380191600, #  local_start 1977-09-30 23:00:00 (Fri)
62398425600, #    local_end 1978-04-30 00:00:00 (Sun)
7200,
0,
'EET',
    ],
    [
62398418400, #    utc_start 1978-04-29 22:00:00 (Sat)
62411634000, #      utc_end 1978-09-29 21:00:00 (Fri)
62398429200, #  local_start 1978-04-30 01:00:00 (Sun)
62411644800, #    local_end 1978-09-30 00:00:00 (Sat)
10800,
1,
'EEST',
    ],
    [
62411634000, #    utc_start 1978-09-29 21:00:00 (Fri)
62616837600, #      utc_end 1985-03-31 22:00:00 (Sun)
62411641200, #  local_start 1978-09-29 23:00:00 (Fri)
62616844800, #    local_end 1985-04-01 00:00:00 (Mon)
7200,
0,
'EET',
    ],
    [
62616837600, #    utc_start 1985-03-31 22:00:00 (Sun)
62632645200, #      utc_end 1985-09-30 21:00:00 (Mon)
62616848400, #  local_start 1985-04-01 01:00:00 (Mon)
62632656000, #    local_end 1985-10-01 00:00:00 (Tue)
10800,
1,
'EEST',
    ],
    [
62632645200, #    utc_start 1985-09-30 21:00:00 (Mon)
62648632800, #      utc_end 1986-04-03 22:00:00 (Thu)
62632652400, #  local_start 1985-09-30 23:00:00 (Mon)
62648640000, #    local_end 1986-04-04 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62648632800, #    utc_start 1986-04-03 22:00:00 (Thu)
62664354000, #      utc_end 1986-10-02 21:00:00 (Thu)
62648643600, #  local_start 1986-04-04 01:00:00 (Fri)
62664364800, #    local_end 1986-10-03 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62664354000, #    utc_start 1986-10-02 21:00:00 (Thu)
62680082400, #      utc_end 1987-04-02 22:00:00 (Thu)
62664361200, #  local_start 1986-10-02 23:00:00 (Thu)
62680089600, #    local_end 1987-04-03 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62680082400, #    utc_start 1987-04-02 22:00:00 (Thu)
62695803600, #      utc_end 1987-10-01 21:00:00 (Thu)
62680093200, #  local_start 1987-04-03 01:00:00 (Fri)
62695814400, #    local_end 1987-10-02 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62695803600, #    utc_start 1987-10-01 21:00:00 (Thu)
62711532000, #      utc_end 1988-03-31 22:00:00 (Thu)
62695810800, #  local_start 1987-10-01 23:00:00 (Thu)
62711539200, #    local_end 1988-04-01 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62711532000, #    utc_start 1988-03-31 22:00:00 (Thu)
62727858000, #      utc_end 1988-10-06 21:00:00 (Thu)
62711542800, #  local_start 1988-04-01 01:00:00 (Fri)
62727868800, #    local_end 1988-10-07 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62727858000, #    utc_start 1988-10-06 21:00:00 (Thu)
62746264800, #      utc_end 1989-05-07 22:00:00 (Sun)
62727865200, #  local_start 1988-10-06 23:00:00 (Thu)
62746272000, #    local_end 1989-05-08 00:00:00 (Mon)
7200,
0,
'EET',
    ],
    [
62746264800, #    utc_start 1989-05-07 22:00:00 (Sun)
62759307600, #      utc_end 1989-10-05 21:00:00 (Thu)
62746275600, #  local_start 1989-05-08 01:00:00 (Mon)
62759318400, #    local_end 1989-10-06 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62759307600, #    utc_start 1989-10-05 21:00:00 (Thu)
62776850400, #      utc_end 1990-04-26 22:00:00 (Thu)
62759314800, #  local_start 1989-10-05 23:00:00 (Thu)
62776857600, #    local_end 1990-04-27 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62776850400, #    utc_start 1990-04-26 22:00:00 (Thu)
62790757200, #      utc_end 1990-10-04 21:00:00 (Thu)
62776861200, #  local_start 1990-04-27 01:00:00 (Fri)
62790768000, #    local_end 1990-10-05 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62790757200, #    utc_start 1990-10-04 21:00:00 (Thu)
62807522400, #      utc_end 1991-04-16 22:00:00 (Tue)
62790764400, #  local_start 1990-10-04 23:00:00 (Thu)
62807529600, #    local_end 1991-04-17 00:00:00 (Wed)
7200,
0,
'EET',
    ],
    [
62807522400, #    utc_start 1991-04-16 22:00:00 (Tue)
62821602000, #      utc_end 1991-09-26 21:00:00 (Thu)
62807533200, #  local_start 1991-04-17 01:00:00 (Wed)
62821612800, #    local_end 1991-09-27 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62821602000, #    utc_start 1991-09-26 21:00:00 (Thu)
62838540000, #      utc_end 1992-04-09 22:00:00 (Thu)
62821609200, #  local_start 1991-09-26 23:00:00 (Thu)
62838547200, #    local_end 1992-04-10 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62838540000, #    utc_start 1992-04-09 22:00:00 (Thu)
62853656400, #      utc_end 1992-10-01 21:00:00 (Thu)
62838550800, #  local_start 1992-04-10 01:00:00 (Fri)
62853667200, #    local_end 1992-10-02 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62853656400, #    utc_start 1992-10-01 21:00:00 (Thu)
62869384800, #      utc_end 1993-04-01 22:00:00 (Thu)
62853663600, #  local_start 1992-10-01 23:00:00 (Thu)
62869392000, #    local_end 1993-04-02 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62869384800, #    utc_start 1993-04-01 22:00:00 (Thu)
62885106000, #      utc_end 1993-09-30 21:00:00 (Thu)
62869395600, #  local_start 1993-04-02 01:00:00 (Fri)
62885116800, #    local_end 1993-10-01 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62885106000, #    utc_start 1993-09-30 21:00:00 (Thu)
62900834400, #      utc_end 1994-03-31 22:00:00 (Thu)
62885113200, #  local_start 1993-09-30 23:00:00 (Thu)
62900841600, #    local_end 1994-04-01 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62900834400, #    utc_start 1994-03-31 22:00:00 (Thu)
62915346000, #      utc_end 1994-09-15 21:00:00 (Thu)
62900845200, #  local_start 1994-04-01 01:00:00 (Fri)
62915356800, #    local_end 1994-09-16 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62915346000, #    utc_start 1994-09-15 21:00:00 (Thu)
62932888800, #      utc_end 1995-04-06 22:00:00 (Thu)
62915353200, #  local_start 1994-09-15 23:00:00 (Thu)
62932896000, #    local_end 1995-04-07 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62932888800, #    utc_start 1995-04-06 22:00:00 (Thu)
62946799200, #      utc_end 1995-09-14 22:00:00 (Thu)
62932899600, #  local_start 1995-04-07 01:00:00 (Fri)
62946810000, #    local_end 1995-09-15 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62946799200, #    utc_start 1995-09-14 22:00:00 (Thu)
62964338400, #      utc_end 1996-04-04 22:00:00 (Thu)
62946806400, #  local_start 1995-09-15 00:00:00 (Fri)
62964345600, #    local_end 1996-04-05 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62964338400, #    utc_start 1996-04-04 22:00:00 (Thu)
62978853600, #      utc_end 1996-09-19 22:00:00 (Thu)
62964349200, #  local_start 1996-04-05 01:00:00 (Fri)
62978864400, #    local_end 1996-09-20 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
62978853600, #    utc_start 1996-09-19 22:00:00 (Thu)
62995788000, #      utc_end 1997-04-03 22:00:00 (Thu)
62978860800, #  local_start 1996-09-20 00:00:00 (Fri)
62995795200, #    local_end 1997-04-04 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
62995788000, #    utc_start 1997-04-03 22:00:00 (Thu)
63010303200, #      utc_end 1997-09-18 22:00:00 (Thu)
62995798800, #  local_start 1997-04-04 01:00:00 (Fri)
63010314000, #    local_end 1997-09-19 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63010303200, #    utc_start 1997-09-18 22:00:00 (Thu)
63027237600, #      utc_end 1998-04-02 22:00:00 (Thu)
63010310400, #  local_start 1997-09-19 00:00:00 (Fri)
63027244800, #    local_end 1998-04-03 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63027237600, #    utc_start 1998-04-02 22:00:00 (Thu)
63041752800, #      utc_end 1998-09-17 22:00:00 (Thu)
63027248400, #  local_start 1998-04-03 01:00:00 (Fri)
63041763600, #    local_end 1998-09-18 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63041752800, #    utc_start 1998-09-17 22:00:00 (Thu)
63066463200, #      utc_end 1999-06-30 22:00:00 (Wed)
63041760000, #  local_start 1998-09-18 00:00:00 (Fri)
63066470400, #    local_end 1999-07-01 00:00:00 (Thu)
7200,
0,
'EET',
    ],
    [
63066463200, #    utc_start 1999-06-30 22:00:00 (Wed)
63073807200, #      utc_end 1999-09-23 22:00:00 (Thu)
63066474000, #  local_start 1999-07-01 01:00:00 (Thu)
63073818000, #    local_end 1999-09-24 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63073807200, #    utc_start 1999-09-23 22:00:00 (Thu)
63090050400, #      utc_end 2000-03-29 22:00:00 (Wed)
63073814400, #  local_start 1999-09-24 00:00:00 (Fri)
63090057600, #    local_end 2000-03-30 00:00:00 (Thu)
7200,
0,
'EET',
    ],
    [
63090050400, #    utc_start 2000-03-29 22:00:00 (Wed)
63105861600, #      utc_end 2000-09-28 22:00:00 (Thu)
63090061200, #  local_start 2000-03-30 01:00:00 (Thu)
63105872400, #    local_end 2000-09-29 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63105861600, #    utc_start 2000-09-28 22:00:00 (Thu)
63121500000, #      utc_end 2001-03-28 22:00:00 (Wed)
63105868800, #  local_start 2000-09-29 00:00:00 (Fri)
63121507200, #    local_end 2001-03-29 00:00:00 (Thu)
7200,
0,
'EET',
    ],
    [
63121500000, #    utc_start 2001-03-28 22:00:00 (Wed)
63137311200, #      utc_end 2001-09-27 22:00:00 (Thu)
63121510800, #  local_start 2001-03-29 01:00:00 (Thu)
63137322000, #    local_end 2001-09-28 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63137311200, #    utc_start 2001-09-27 22:00:00 (Thu)
63153036000, #      utc_end 2002-03-28 22:00:00 (Thu)
63137318400, #  local_start 2001-09-28 00:00:00 (Fri)
63153043200, #    local_end 2002-03-29 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63153036000, #    utc_start 2002-03-28 22:00:00 (Thu)
63168760800, #      utc_end 2002-09-26 22:00:00 (Thu)
63153046800, #  local_start 2002-03-29 01:00:00 (Fri)
63168771600, #    local_end 2002-09-27 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63168760800, #    utc_start 2002-09-26 22:00:00 (Thu)
63184485600, #      utc_end 2003-03-27 22:00:00 (Thu)
63168768000, #  local_start 2002-09-27 00:00:00 (Fri)
63184492800, #    local_end 2003-03-28 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63184485600, #    utc_start 2003-03-27 22:00:00 (Thu)
63202629600, #      utc_end 2003-10-23 22:00:00 (Thu)
63184496400, #  local_start 2003-03-28 01:00:00 (Fri)
63202640400, #    local_end 2003-10-24 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63202629600, #    utc_start 2003-10-23 22:00:00 (Thu)
63215935200, #      utc_end 2004-03-25 22:00:00 (Thu)
63202636800, #  local_start 2003-10-24 00:00:00 (Fri)
63215942400, #    local_end 2004-03-26 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63215935200, #    utc_start 2004-03-25 22:00:00 (Thu)
63233474400, #      utc_end 2004-10-14 22:00:00 (Thu)
63215946000, #  local_start 2004-03-26 01:00:00 (Fri)
63233485200, #    local_end 2004-10-15 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63233474400, #    utc_start 2004-10-14 22:00:00 (Thu)
63247989600, #      utc_end 2005-03-31 22:00:00 (Thu)
63233481600, #  local_start 2004-10-15 00:00:00 (Fri)
63247996800, #    local_end 2005-04-01 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63247989600, #    utc_start 2005-03-31 22:00:00 (Thu)
63263714400, #      utc_end 2005-09-29 22:00:00 (Thu)
63248000400, #  local_start 2005-04-01 01:00:00 (Fri)
63263725200, #    local_end 2005-09-30 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63263714400, #    utc_start 2005-09-29 22:00:00 (Thu)
63279439200, #      utc_end 2006-03-30 22:00:00 (Thu)
63263721600, #  local_start 2005-09-30 00:00:00 (Fri)
63279446400, #    local_end 2006-03-31 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63279439200, #    utc_start 2006-03-30 22:00:00 (Thu)
63297583200, #      utc_end 2006-10-26 22:00:00 (Thu)
63279450000, #  local_start 2006-03-31 01:00:00 (Fri)
63297594000, #    local_end 2006-10-27 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63297583200, #    utc_start 2006-10-26 22:00:00 (Thu)
63310888800, #      utc_end 2007-03-29 22:00:00 (Thu)
63297590400, #  local_start 2006-10-27 00:00:00 (Fri)
63310896000, #    local_end 2007-03-30 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63310888800, #    utc_start 2007-03-29 22:00:00 (Thu)
63329032800, #      utc_end 2007-10-25 22:00:00 (Thu)
63310899600, #  local_start 2007-03-30 01:00:00 (Fri)
63329043600, #    local_end 2007-10-26 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63329032800, #    utc_start 2007-10-25 22:00:00 (Thu)
63342338400, #      utc_end 2008-03-27 22:00:00 (Thu)
63329040000, #  local_start 2007-10-26 00:00:00 (Fri)
63342345600, #    local_end 2008-03-28 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63342338400, #    utc_start 2008-03-27 22:00:00 (Thu)
63361087200, #      utc_end 2008-10-30 22:00:00 (Thu)
63342349200, #  local_start 2008-03-28 01:00:00 (Fri)
63361098000, #    local_end 2008-10-31 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63361087200, #    utc_start 2008-10-30 22:00:00 (Thu)
63373788000, #      utc_end 2009-03-26 22:00:00 (Thu)
63361094400, #  local_start 2008-10-31 00:00:00 (Fri)
63373795200, #    local_end 2009-03-27 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63373788000, #    utc_start 2009-03-26 22:00:00 (Thu)
63392536800, #      utc_end 2009-10-29 22:00:00 (Thu)
63373798800, #  local_start 2009-03-27 01:00:00 (Fri)
63392547600, #    local_end 2009-10-30 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63392536800, #    utc_start 2009-10-29 22:00:00 (Thu)
63405237600, #      utc_end 2010-03-25 22:00:00 (Thu)
63392544000, #  local_start 2009-10-30 00:00:00 (Fri)
63405244800, #    local_end 2010-03-26 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63405237600, #    utc_start 2010-03-25 22:00:00 (Thu)
63423986400, #      utc_end 2010-10-28 22:00:00 (Thu)
63405248400, #  local_start 2010-03-26 01:00:00 (Fri)
63423997200, #    local_end 2010-10-29 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63423986400, #    utc_start 2010-10-28 22:00:00 (Thu)
63437292000, #      utc_end 2011-03-31 22:00:00 (Thu)
63423993600, #  local_start 2010-10-29 00:00:00 (Fri)
63437299200, #    local_end 2011-04-01 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63437292000, #    utc_start 2011-03-31 22:00:00 (Thu)
63455436000, #      utc_end 2011-10-27 22:00:00 (Thu)
63437302800, #  local_start 2011-04-01 01:00:00 (Fri)
63455446800, #    local_end 2011-10-28 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63455436000, #    utc_start 2011-10-27 22:00:00 (Thu)
63468741600, #      utc_end 2012-03-29 22:00:00 (Thu)
63455443200, #  local_start 2011-10-28 00:00:00 (Fri)
63468748800, #    local_end 2012-03-30 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63468741600, #    utc_start 2012-03-29 22:00:00 (Thu)
63523170000, #      utc_end 2013-12-19 21:00:00 (Thu)
63468752400, #  local_start 2012-03-30 01:00:00 (Fri)
63523180800, #    local_end 2013-12-20 00:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63523170000, #    utc_start 2013-12-19 21:00:00 (Thu)
63531640800, #      utc_end 2014-03-27 22:00:00 (Thu)
63523177200, #  local_start 2013-12-19 23:00:00 (Thu)
63531648000, #    local_end 2014-03-28 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63531640800, #    utc_start 2014-03-27 22:00:00 (Thu)
63550389600, #      utc_end 2014-10-30 22:00:00 (Thu)
63531651600, #  local_start 2014-03-28 01:00:00 (Fri)
63550400400, #    local_end 2014-10-31 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63550389600, #    utc_start 2014-10-30 22:00:00 (Thu)
63563090400, #      utc_end 2015-03-26 22:00:00 (Thu)
63550396800, #  local_start 2014-10-31 00:00:00 (Fri)
63563097600, #    local_end 2015-03-27 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63563090400, #    utc_start 2015-03-26 22:00:00 (Thu)
63581839200, #      utc_end 2015-10-29 22:00:00 (Thu)
63563101200, #  local_start 2015-03-27 01:00:00 (Fri)
63581850000, #    local_end 2015-10-30 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63581839200, #    utc_start 2015-10-29 22:00:00 (Thu)
63595144800, #      utc_end 2016-03-31 22:00:00 (Thu)
63581846400, #  local_start 2015-10-30 00:00:00 (Fri)
63595152000, #    local_end 2016-04-01 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63595144800, #    utc_start 2016-03-31 22:00:00 (Thu)
63613288800, #      utc_end 2016-10-27 22:00:00 (Thu)
63595155600, #  local_start 2016-04-01 01:00:00 (Fri)
63613299600, #    local_end 2016-10-28 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63613288800, #    utc_start 2016-10-27 22:00:00 (Thu)
63626594400, #      utc_end 2017-03-30 22:00:00 (Thu)
63613296000, #  local_start 2016-10-28 00:00:00 (Fri)
63626601600, #    local_end 2017-03-31 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63626594400, #    utc_start 2017-03-30 22:00:00 (Thu)
63644738400, #      utc_end 2017-10-26 22:00:00 (Thu)
63626605200, #  local_start 2017-03-31 01:00:00 (Fri)
63644749200, #    local_end 2017-10-27 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63644738400, #    utc_start 2017-10-26 22:00:00 (Thu)
63658044000, #      utc_end 2018-03-29 22:00:00 (Thu)
63644745600, #  local_start 2017-10-27 00:00:00 (Fri)
63658051200, #    local_end 2018-03-30 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63658044000, #    utc_start 2018-03-29 22:00:00 (Thu)
63676188000, #      utc_end 2018-10-25 22:00:00 (Thu)
63658054800, #  local_start 2018-03-30 01:00:00 (Fri)
63676198800, #    local_end 2018-10-26 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63676188000, #    utc_start 2018-10-25 22:00:00 (Thu)
63689493600, #      utc_end 2019-03-28 22:00:00 (Thu)
63676195200, #  local_start 2018-10-26 00:00:00 (Fri)
63689500800, #    local_end 2019-03-29 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63689493600, #    utc_start 2019-03-28 22:00:00 (Thu)
63707637600, #      utc_end 2019-10-24 22:00:00 (Thu)
63689504400, #  local_start 2019-03-29 01:00:00 (Fri)
63707648400, #    local_end 2019-10-25 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63707637600, #    utc_start 2019-10-24 22:00:00 (Thu)
63720943200, #      utc_end 2020-03-26 22:00:00 (Thu)
63707644800, #  local_start 2019-10-25 00:00:00 (Fri)
63720950400, #    local_end 2020-03-27 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63720943200, #    utc_start 2020-03-26 22:00:00 (Thu)
63739692000, #      utc_end 2020-10-29 22:00:00 (Thu)
63720954000, #  local_start 2020-03-27 01:00:00 (Fri)
63739702800, #    local_end 2020-10-30 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63739692000, #    utc_start 2020-10-29 22:00:00 (Thu)
63752392800, #      utc_end 2021-03-25 22:00:00 (Thu)
63739699200, #  local_start 2020-10-30 00:00:00 (Fri)
63752400000, #    local_end 2021-03-26 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63752392800, #    utc_start 2021-03-25 22:00:00 (Thu)
63771141600, #      utc_end 2021-10-28 22:00:00 (Thu)
63752403600, #  local_start 2021-03-26 01:00:00 (Fri)
63771152400, #    local_end 2021-10-29 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63771141600, #    utc_start 2021-10-28 22:00:00 (Thu)
63784447200, #      utc_end 2022-03-31 22:00:00 (Thu)
63771148800, #  local_start 2021-10-29 00:00:00 (Fri)
63784454400, #    local_end 2022-04-01 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63784447200, #    utc_start 2022-03-31 22:00:00 (Thu)
63802591200, #      utc_end 2022-10-27 22:00:00 (Thu)
63784458000, #  local_start 2022-04-01 01:00:00 (Fri)
63802602000, #    local_end 2022-10-28 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63802591200, #    utc_start 2022-10-27 22:00:00 (Thu)
63815896800, #      utc_end 2023-03-30 22:00:00 (Thu)
63802598400, #  local_start 2022-10-28 00:00:00 (Fri)
63815904000, #    local_end 2023-03-31 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63815896800, #    utc_start 2023-03-30 22:00:00 (Thu)
63834040800, #      utc_end 2023-10-26 22:00:00 (Thu)
63815907600, #  local_start 2023-03-31 01:00:00 (Fri)
63834051600, #    local_end 2023-10-27 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63834040800, #    utc_start 2023-10-26 22:00:00 (Thu)
63847346400, #      utc_end 2024-03-28 22:00:00 (Thu)
63834048000, #  local_start 2023-10-27 00:00:00 (Fri)
63847353600, #    local_end 2024-03-29 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63847346400, #    utc_start 2024-03-28 22:00:00 (Thu)
63865490400, #      utc_end 2024-10-24 22:00:00 (Thu)
63847357200, #  local_start 2024-03-29 01:00:00 (Fri)
63865501200, #    local_end 2024-10-25 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63865490400, #    utc_start 2024-10-24 22:00:00 (Thu)
63878796000, #      utc_end 2025-03-27 22:00:00 (Thu)
63865497600, #  local_start 2024-10-25 00:00:00 (Fri)
63878803200, #    local_end 2025-03-28 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63878796000, #    utc_start 2025-03-27 22:00:00 (Thu)
63897544800, #      utc_end 2025-10-30 22:00:00 (Thu)
63878806800, #  local_start 2025-03-28 01:00:00 (Fri)
63897555600, #    local_end 2025-10-31 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63897544800, #    utc_start 2025-10-30 22:00:00 (Thu)
63910245600, #      utc_end 2026-03-26 22:00:00 (Thu)
63897552000, #  local_start 2025-10-31 00:00:00 (Fri)
63910252800, #    local_end 2026-03-27 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63910245600, #    utc_start 2026-03-26 22:00:00 (Thu)
63928994400, #      utc_end 2026-10-29 22:00:00 (Thu)
63910256400, #  local_start 2026-03-27 01:00:00 (Fri)
63929005200, #    local_end 2026-10-30 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
    [
63928994400, #    utc_start 2026-10-29 22:00:00 (Thu)
63941695200, #      utc_end 2027-03-25 22:00:00 (Thu)
63929001600, #  local_start 2026-10-30 00:00:00 (Fri)
63941702400, #    local_end 2027-03-26 00:00:00 (Fri)
7200,
0,
'EET',
    ],
    [
63941695200, #    utc_start 2027-03-25 22:00:00 (Thu)
63960444000, #      utc_end 2027-10-28 22:00:00 (Thu)
63941706000, #  local_start 2027-03-26 01:00:00 (Fri)
63960454800, #    local_end 2027-10-29 01:00:00 (Fri)
10800,
1,
'EEST',
    ],
];

sub olson_version {'2016c'}

sub has_dst_changes {48}

sub _max_year {2026}

sub _new_instance {
    return shift->_init( @_, spans => $spans );
}

sub _last_offset { 7200 }

my $last_observance = bless( {
  'format' => 'EE%sT',
  'gmtoff' => '2:00',
  'local_start_datetime' => bless( {
    'formatter' => undef,
    'local_rd_days' => 704917,
    'local_rd_secs' => 84976,
    'offset_modifier' => 0,
    'rd_nanosecs' => 0,
    'tz' => bless( {
      'name' => 'floating',
      'offset' => 0
    }, 'DateTime::TimeZone::Floating' ),
    'utc_rd_days' => 704917,
    'utc_rd_secs' => 84976,
    'utc_year' => 1931
  }, 'DateTime' ),
  'offset_from_std' => 0,
  'offset_from_utc' => 7200,
  'until' => [],
  'utc_start_datetime' => bless( {
    'formatter' => undef,
    'local_rd_days' => 704917,
    'local_rd_secs' => 77776,
    'offset_modifier' => 0,
    'rd_nanosecs' => 0,
    'tz' => bless( {
      'name' => 'floating',
      'offset' => 0
    }, 'DateTime::TimeZone::Floating' ),
    'utc_rd_days' => 704917,
    'utc_rd_secs' => 77776,
    'utc_year' => 1931
  }, 'DateTime' )
}, 'DateTime::TimeZone::OlsonDB::Observance' )
;
sub _last_observance { $last_observance }

my $rules = [
  bless( {
    'at' => '24:00',
    'from' => '2014',
    'in' => 'Mar',
    'letter' => 'S',
    'name' => 'Jordan',
    'offset_from_std' => 3600,
    'on' => 'lastThu',
    'save' => '1:00',
    'to' => 'max',
    'type' => undef
  }, 'DateTime::TimeZone::OlsonDB::Rule' ),
  bless( {
    'at' => '0:00s',
    'from' => '2014',
    'in' => 'Oct',
    'letter' => '',
    'name' => 'Jordan',
    'offset_from_std' => 0,
    'on' => 'lastFri',
    'save' => '0',
    'to' => 'max',
    'type' => undef
  }, 'DateTime::TimeZone::OlsonDB::Rule' )
]
;
sub _rules { $rules }


1;

