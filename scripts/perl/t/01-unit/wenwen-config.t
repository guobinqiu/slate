BEGIN { do 't/test-env.pl' }

use common::sense;

use Test::More;
use Test::Pretty;

my $class = 'Wenwen::Config';
use_ok $class;

subtest get_environment_name => sub {
    local $ENV{WENWEN_ENV} = '12345';
    is $class->get_environment_name, '12345';
};

subtest is_deployment => sub {
    subtest 'test' => sub {
        local $ENV{WENWEN_ENV} = 'test';
        ok ! $class->is_deployment;
    };
    subtest 'development' => sub {
        local $ENV{WENWEN_ENV} = 'development';
        is $class->param('is_test'), 1;
        ok ! $class->is_deployment;
    };
    subtest 'deployment' => sub {
        local $ENV{WENWEN_ENV} = 'deployment';
        is $class->param('is_test'), 0;
        ok $class->is_deployment;
    };
};

done_testing;

__DATA__

