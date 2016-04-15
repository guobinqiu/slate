package Wenwen::Config;
use common::sense;

use Config::ENV 'WENWEN_ENV', export => 'c';
use Wenwen::Util ();

BEGIN {
    if (!defined $ENV{WENWEN_ENV}) {
        my $file = Wenwen::Util::path_to('env.pl');
        do $file or die "env.pl not found!: $!";
    }
}

common + { load(Wenwen::Util::path_to('config/deployment.pl')), };

config development => +{ load(Wenwen::Util::path_to('config/development.pl')), };

config 'development-vagrant' => +{ parent('development'), };
config 'development-circle' => +{ parent('development'), };

sub is_deployment {
    get_environment_name() eq 'deployment';
}
sub get_environment_name { $ENV{WENWEN_ENV} }

1;
