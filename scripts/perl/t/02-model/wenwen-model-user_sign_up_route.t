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
            [
            {   source_route           => 'NULL',
                count                  => 1,
                register_complete_date => '2016-04-13',
            },
            {   source_route           => 'google',
                count                  => 1,
                register_complete_date => '2016-04-14',
            },
            {   source_route           => 'baidu',
                count                  => 2,
                register_complete_date => '2016-04-15',
            },
            ];
    };
    subtest 'w/ query' => sub {
        my $res = $class->retrieve_route_summary(
            $handle,
            {   register_complete_date_from => '2016-04-14',
                register_complete_date_to   => '2016-04-15',
            }
        );
        is_deeply $res,
            [
            {   source_route           => 'google',
                count                  => 1,
                register_complete_date => '2016-04-14',
            },
            ];
    };
};

done_testing;

__DATA__

@@ jili_db

- user:
    - id: 1
      nick: test
      email: test+1@d8aspring.com
      pwd: password
      token: hoge
      reward_multiple: 1
      points: 10
      is_info_set: 1
      register_complete_date: 2016-04-15
    - id: 2
      nick: test
      email: test+2@d8aspring.com
      pwd: password
      token: hoge
      reward_multiple: 1
      points: 10
      is_info_set: 1
      register_complete_date: 2016-04-15
    - id: 3
      nick: test
      email: test+3@d8aspring.com
      pwd: password
      token: hoge
      reward_multiple: 1
      points: 10
      is_info_set: 1
      register_complete_date: 2016-04-14
    - id: 4
      nick: test
      email: test+4@d8aspring.com
      pwd: password
      token: hoge
      reward_multiple: 1
      points: 10
      is_info_set: 1
      register_complete_date: 2016-04-13

- user_sign_up_route:
    - id: 1
      user_id: 1
      source_route: baidu
      created_time: '2016-04-15'
    - id: 2
      user_id: 2
      source_route: baidu
      created_time: '2016-04-15'
    - id: 3
      user_id: 3
      source_route: google
      created_time: '2016-04-14'
