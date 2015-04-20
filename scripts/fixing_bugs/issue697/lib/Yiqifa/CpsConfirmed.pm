package Yiqifa::CpsConfirmed;

# the files area donwload from the yiqifa backend
sub get_confirmed_utf8_filelist{
    my($path_dest)  = @_;
    opendir(my $dh, $path_dest )or  die $!;

    my $file_reg = '_utf8.csv$';
    my $files=[];
    while (my $file = readdir($dh)) {
        next unless (-f "$path_dest/$file");
        # Use a regular expression to find files ending in .txt
        next unless ( "$path_dest/$file" =~ m/$file_reg/);
        unshift $files , ( "$path_dest/$file" );
    }
    return $files;
}

1;
__END__
