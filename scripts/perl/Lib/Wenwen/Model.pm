package Wenwen::Model;
use common::sense;
use parent qw(Teng);
__PACKAGE__->load_plugin('Count');
__PACKAGE__->load_plugin('SearchJoined');

use Wenwen::Config;

our %ConnectOptions = (mysql_enable_utf8 => 1,);

# mysql_tzinfo_to_sql /usr/share/zoneinfo | mysql -uroot mysql
our %ExtraOptions   = (on_connect_do     => [ 'SET time_zone = "Asia/Shanghai"', ],);

sub create_handle {
    my $class = shift;
    my $db_config = c->param('database');
    $class->new(
        {
            connect_info => [
                map { $db_config->{$_} } qw(dsn user pass),
                \%ConnectOptions,
            ],
            schema_class => 'Wenwen::Model::Schema',
            %ExtraOptions,
        }
    );
}

sub get_slack_url {
    c->param('slack_url');
}