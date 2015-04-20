package Jili::DBConnection;
$VERSION = v0.0.1;

use warnings;
use strict;

use base 'Class::Singleton';
use DBI;

# this only gets called the first time instance() is called
sub _new_instance {
    my $class    = shift;
    my $self     = bless { }, $class;
    my $user     = shift || "root";
    my $password = shift || "MyNewPassword";
    my $database = shift || "jili_1216"; #jili_1117";
    my $hostname = shift || "localhost"; #192.168.1.70";
    my $port     = shift || 3306;

    my $dsn = "DBI:mysql:database=$database;host=$hostname;port=$port";
    #my $dbh = DBI->connect($dsn, $user, $password,{ RaiseError => 1, AutoCommit => 0 });

    $self->{dbh} = DBI->connect($dsn, $user, $password,{ RaiseError => 1, AutoCommit => 0 })
    || die "Cannot connect to database: $DBI::errstr";

    $self->{dbh}->do( "set names utf8" );
    # any other initialisation...
    return $self;
}

__END__

