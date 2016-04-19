package TestFixture::Wenwen;

BEGIN {
    use Wenwen::Config ();
    die __PACKAGE__ . " should not be used in deployment environment!!!!!"
        if Wenwen::Config->is_deployment;
}

use common::sense;
use Wenwen::Model;

sub build_database {
    my ($class, $data) = @_;
    my $handle = Wenwen::Model->create_handle;

    $class->destroy_tables($handle);
    for my $table_data (@$data) {
        my ($table_name, $rows) = %$table_data;
        $handle->insert($table_name => $_) for @$rows;
    }
}

sub destroy_tables {
    my ($class, $handle) = @_;

    my @tables = do {
        my @tables = ();

        my $sth = $handle->dbh->prepare(qq| SHOW FULL TABLES WHERE Table_type != 'VIEW' |);
        $sth->execute;

        while (my $res = $sth->fetchrow_arrayref) {
            push @tables, $res->[0];
        }
        $sth->finish;
        @tables;
    };

    $handle->dbh->do('SET foreign_key_checks = 0');
    $handle->dbh->do("TRUNCATE TABLE $_") for @tables;
    $handle->dbh->do('SET foreign_key_checks = 1');
}

1;

