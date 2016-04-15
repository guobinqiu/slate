BEGIN { do 't/test-env.pl' }

use common::sense;

use Test::More;
use Test::Pretty;
use TestFixture::Wenwen;
use TestFixture::Util qw(get_database_data);

my $class = 'Wenwen::Model::UserSignUpRoute';

use_ok $class;

TestFixture::Wenwen->build_database(get_database_data('jili_db'));


done_testing;

__DATA__

@@ jili_db

- user:
    : for [1,2,3] -> $id {
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
