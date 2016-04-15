BEGIN { do 't/test-env.pl' }

use common::sense;

use Test::More;
use Test::Pretty;
use TestFixture::Wenwen;
use TestFixture::Util qw(get_database_data);
use Time::Piece ();
use Time::Seconds;
use Wenwen::Model;

my $class = 'Wenwen::Model::UserSignUpRoute';

use_ok $class;

my $handle = Wenwen::Model->create_handle;

TestFixture::Wenwen->build_database(get_database_data('jili_db'));

subtest retrieve_route_summary => sub {
    subtest 'w/o query' => sub {
        my $res = $class->retrieve_route_summary($handle);
        is_deeply $res,
            {
            baidu => {
                source_route => 'baidu',
                count        => 2,
            },
            google => {
                source_route => 'google',
                count        => 1,
            },
            NULL => {
                source_route => 'NULL',
                count        => 1,
            },
            };
    };
    subtest 'w/ query' => sub {
        my $res = $class->retrieve_route_summary(
            $handle,
            {   register_complete_date_from => (Time::Piece->localtime - ONE_DAY)->strftime('%F'),
                register_complete_date_to   => Time::Piece->localtime->strftime('%F'),
            }
        );
        is_deeply $res,
            {
            baidu => {
                source_route => 'baidu',
                count        => 1,
            },
            };
    };
};

done_testing;

__DATA__

@@ jili_db

- user:
    : for [1,2,3,4] -> $id {
    - id: <: $id :>
      nick: test
      email: test+<: $id :>@d8aspring.com
      pwd: password
      token: hoge
      reward_multiple: 1
      points: 10
      is_info_set: 1
      register_complete_date: <: add_days($tp, -1 * $id).strftime('%F %T'):>
    : }

- user_sign_up_route:
    - id: 1
      user_id: 1
      source_route: baidu
      created_time: <: add_days($tp, -1 ).strftime('%F %T'):>
    - id: 2
      user_id: 2
      source_route: baidu
      created_time: <: add_days($tp, -2 ).strftime('%F %T'):>
    - id: 3
      user_id: 3
      source_route: google
      created_time: <: add_days($tp, -3 ).strftime('%F %T'):>
