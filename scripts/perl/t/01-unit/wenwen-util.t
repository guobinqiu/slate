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

done_testing;

__DATA__

