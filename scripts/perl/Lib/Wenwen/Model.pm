package Wenwen::Model;
use common::sense;
use parent qw(Teng);
__PACKAGE__->load_plugin('Count');
__PACKAGE__->load_plugin('SearchJoined');

our %ConnectOptions = (
    mysql_enable_utf8 => 1,
);
our %ExtraOptions = (
    on_connect_do => [
        'SET time_zone = "Asia/Shanghai"',
    ],
);

sub create_handle {
    my $class = shift;
    ## Read config infomations from config files
    $class->_new();
}

## Todo split dsn and etc. into config files
sub _new {
    my ($class, $config) = @_;
    $class->new({
        connect_info => [
            'dbi:mysql:dbname=jili_db',
            'root',
            '',
            \%ConnectOptions,
        ],
        ## Todo split schema package name into config files
        schema_class => 'Wenwen::Model::Schema',
        %ExtraOptions,
    });
}
