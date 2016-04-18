BEGIN { do 't/test-env.pl' }

use common::sense;
use Test::More;
use Test::Pretty;

use_ok('Wenwen::Util');

subtest 'home, path_to' => sub {

    my $home = Wenwen::Util::home();
    ok -d $home, 'home directory is ' . $home;

    my $path = Wenwen::Util::path_to('config');
    ok -d $path, 'config directory is ' . $path;
};

subtest 'Test inflate_timestamp/deflate_timestamp' => sub {

    subtest 'Inflate acceptable datetime format' => sub {
        my $tp = Wenwen::Util::inflate_timestamp('2012-02-30 00:00:00');

        isa_ok($tp, 'Time::Piece', 'inflation');
        is($tp->ymd, '2012-03-01', 'ymd is 2012-03-01');
    };

    subtest 'Inflate undef' => sub {
        my $res = Wenwen::Util::inflate_timestamp(undef);

        is $res, undef, 'passing undef, getting undef';
    };

    subtest 'Deflate acceptable object' => sub {
        my $tp = Time::Piece->strptime('2012-03-01 00:00:00', '%Y-%m-%d %T');

        my $timestamp = Wenwen::Util::deflate_timestamp($tp);

        is($timestamp, '2012-03-01 00:00:00', 'T::P object is now a string: ' . $timestamp);
    };

    subtest 'Deflate SCALAR-ref' => sub {
        my $val = 'NOW()';

        my $res = Wenwen::Util::deflate_timestamp(\$val);

        isa_ok $res, 'SCALAR';
        is $$res,    'NOW()';
    };

    subtest 'Deflate undef' => sub {
        my $res = Wenwen::Util::deflate_timestamp(undef);

        is $res, undef, 'passing undef, getting undef';
    };
};

done_testing;

__DATA__

