#!/usr/bin/perl

use DBI;
use Wenwen::Email;

my $driver = "DBI:mysql";
my $database = "jili_db";
my $user = "user";
my $passwd = "passwd";
my $s_host = "S_HOST";
my $db_host = "DB_HOST";
my $s_port = "S_PORT";
my $base = 1000;

sub collect_pending () {
    my $dbh = DBI->connect("$driver:database=$database;host=$db_host;user=$user;password=$passwd");

    my $sth = $dbh->prepare("select count(*) from jms_jobs where state = 'pending'");
    $sth->execute();
    while(my $ref = $sth->fetchrow_hashref()){
        $pending = $ref->{'count(*)'};
    }

    $sth->finish();
    $dbh->disconnect();
}

sub rest_job () {
    if ($pending > $base) {
        my $restart_stat = system("ssh -p $S_port $server_host 'service supervisord restart'");
        if ($restart_stat == 0) {
            &sendmail;
            print "restart job success\n";
        } else {
            print "restart error\n";
        }
    } else { 
        print "job works normal\n";
    }        
}

sub sendmail () {
    my $to      = 'rpa-sys-china@d8aspring.com';
    my $subject = "91wenwen - Pending mail more than $base restart supervisord";
    my $body    = "Pending mail more than $base restart supervisord";
    
    my $sender = Wenwen::Email->new();
    $sender->send($to, $subject, $body) or die "Send fail";
}

sub main () {
    collect_pending;
    rest_job
}

main

