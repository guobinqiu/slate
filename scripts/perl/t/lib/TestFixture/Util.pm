package TestFixture::Util;
use common::sense;
use Data::Section::Simple;
use Exporter qw(import);
use Text::Xslate;
use Time::Piece ();
use Time::Seconds;
use YAML;

our @EXPORT_OK = qw(get_database_data);

sub xslate {
    Text::Xslate->new(
        cache    => 0,
        function => {
            add_days => sub {
                $_[0] + $_[1] * ONE_DAY;
            },
        },
    );
}

sub render_xslate {
    my $data = shift;
    my $tp   = Time::Piece->localtime;
    xslate->render_string($data, { tp => $tp });
}

sub get_database_data {
    my @section_names = @_;

    my @table_array = ();
    for my $section_name (@section_names) {
        my $yaml = render_xslate(Data::Section::Simple->new(caller)->get_data_section($section_name));
        push @table_array, @{ YAML::Load($yaml) };
    }
    \@table_array;
}

1;
