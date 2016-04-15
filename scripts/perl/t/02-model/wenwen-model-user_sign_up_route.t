BEGIN { do 't/test-env.pl' }

use common::sense;

use Test::More;
use Test::Pretty;

my $class = 'Wenwen::Model::UserSignUpRoute';

use_ok $class;

done_testing;

__DATA__

