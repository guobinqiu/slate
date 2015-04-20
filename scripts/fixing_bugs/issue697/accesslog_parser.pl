#!/usr/bin/perl

use strict;
use warnings;

use Time::Piece;
use Text::CSV;
use Digest::MD5 qw{md5_hex};  
use utf8;
use Encode qw(from_to);
use URI::Escape;

binmode(STDIN, ':encoding(utf8)');
#binmode(STDOUT, ':encoding(utf8)');
binmode(STDERR, ':encoding(utf8)');

sub accesslog_parser {

    my ($files ) = @_;
# read file

    my @queryHashes ;

    foreach my $file ( @$files) {
        print $file,"\n";
        open(my $fh, '<:encoding(utf8)', $file ) or die "Could not open '$file' $!\n";
        my $i = 0;

#        my $csv = Text::CSV->new ({auto_diag => 1, binary=>1,allow_whitespace=>1 })  # should set binary attribute.
#$            or die "Cannot use CSV: ".Text::CSV->error_diag ();

#221.122.127.42 - - [22/Mar/2015:07:21:21 +0800] "GET /emar/api/callback?unique_id=936dc2f355e0605c48e511a919a19c6f&create_date=2015-03-22+07%3A20%3A58&action_id=249&action_name=%BA%EC%BA%A2%D7%D3CPS&sid=458631&wid=732204&order_no=1015860739&order_time=2015-03-21+15%3A41%3A33&prod_id=1083515536&prod_name=105322263&prod_count=1&prod_money=458.0&feed_back=1100438&status=R&comm_type=%C4%B8%D3%A4&commision=6.412&chkcode=ce9b80d5ddeaafba29c9b49a380a616a&prod_type=%C4%B8%D3%A4&am=0.0&exchange_rate=0.0&superrebate= HTTP/1.1" 403 299 "-" "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1) )"

        my $log_pattern = qr{(.*) \- \- \[(.*)\] \"(.*) (.*)\?(.*) HTTP\/(.*)\" ([0-9]*) ([0-9]*) \"(.*)\" \"(.*)\"};
        #                            1 ,         2 ,      3    4      5           6      7        8        -        9 
        #                            ip,         time,  method path  qs           ver   status   length    -         ua
        while ( my $entry = <$fh> ) {
            $entry =~ $log_pattern;
            print "\n";
            print '   query_string: ',$5,"\n";
            my $hash = md5_hex($5);


            if( grep{$_ eq $hash } @queryHashes ) {
                next;
            };
            push @queryHashes , ($hash);

            print '   hash: ',$hash,"\n";
            print '   ip: ',$1,"\n";
#            16/Mar/2015:11:33:25 +0800;
            my $t = Time::Piece->strptime(substr($2,0,20), "%d/%b/%Y:%H:%M:%S");
            print '   loggged_at: ',$t->strftime("%Y-%m-%d %H:%M:%S") ,"\n";
            print "\n";
            my $qs  = parseQueryString($5);

            print '   ',$_,' --> ' ,$qs->{$_},   "\n" for( keys %$qs);

            print "\n";
        }

        close($fh)|| warn "close failed: $!";
    }
}

# return hash ref 
sub parseQueryString{
    my $in = {};

    my ($qs) = @_;
    if (length ($qs) > 0){
        my @pairs = split(/&/, $qs);
        foreach my $pair (@pairs){
            my ($name, $value) = split(/=/, $pair);
            $value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
#            if( $name eq 'action_name' || $name eq 'prod_type' || $name eq 'comm_type'  ) {
#                from_to($value, "gbk", "utf-8");
#            }
            $in->{$name} = $value; 
        }
    }
    return $in;
}


my $files= [
    'accesslogs/access_log-20150322',
    'accesslogs/access_log-20150329',
    'accesslogs/access_log-20150405',
    'accesslogs/access_log-20150412',
    'accesslogs/access_log',
];

accesslog_parser( $files);
__END__

