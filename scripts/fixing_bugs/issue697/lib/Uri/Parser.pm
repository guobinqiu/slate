package Uri::Parser;
use Encode qw(from_to);

sub parseQueryStringRaw {
    my $in = {};
    my ($qs) = @_;
    $qs = substr($qs, index($qs  , '?')+1 );
    if (length ($qs) > 0){
        my @pairs = split(/&/, $qs);
        foreach my $pair (@pairs){
            my ($name, $value) = split(/=/, $pair);
            $in->{$name} = $value; 
        }
    }
    return $in;
}

sub parseQueryString {
    my $in = {};
    my ($qs) = @_;
    $qs = substr($qs, index($qs  , '?')+1 );
    if (length ($qs) > 0){
        my @pairs = split(/&/, $qs);
        foreach my $pair (@pairs){
            my ($name, $value) = split(/=/, $pair);
            $value =~ s/%([a-fA-F0-9][a-fA-F0-9])/pack("C", hex($1))/eg;
            if( $name eq 'action_name' || $name eq 'prod_type' || $name eq 'comm_type'  ) {
                from_to($value, "gbk", "utf-8");
            }
            $in->{$name} = $value; 
        }
    }
    return $in;
}

1;

__END__
