#!/user/bin/perl

use YAML::XS;
use POSIX qw(strftime);
use Wenwen::Email;
use Tie::File;

our $day = strftime("%Y-%m-%d", localtime(time - 24*3600));
my $resultfile = "/tmp/result.log";

sub config {
    my $config=eval{YAML::XS::LoadFile('./config/monitor_log.yaml')};
    if (defined($config)) {
        our $mail_to = $config->{mail_to}->{to};
        our $prod_dir = $config->{dir}->{prod};
        our $prod_file = $config->{file}->{prod};
        our $prod_keyword = $config->{keywords}->{prod};
        our $prod_full_file = $prod_dir.$prod_file;
    }else{
        my $mail_subject = "Please check config file";
        my $mail_content = "Please check config file";
        my $sender = Wenwen::Email->new();
        $sender->send($mail_to,$mail_subject,$mail_content) or die "";
    }
}

sub filter {
    my ($logfile) = @_;
    if (-e $logfile) {
        $prod_keyword =~ s/,\ /|/;
        open(PROD_LOG,"< $logfile")||die"cannot open the file: $!"; 
        open(OUTFILE,"> $resultfile")||die"cannot open the file: $!";
        while (<PROD_LOG>) {
            if (/\[$day/) {
                if (/$prod_keyword/) {
                    chomp;
                    my @row = split /\ /;
                    my $source = @row[-3];
                    if ($tmp != $source || $tmp ne $source) {
                        print OUTFILE "@row\n";
                        $tmp = $source;
                    }
                }
            }
        }
        close PROD_LOG; 
        close OUTFILE;
    }else{
        my $mail_subject = "Please check log file";
        my $mail_content = "Please check log file";
        my $sender = Wenwen::Email->new();
        $sender->send($mail_to,$mail_subject,$mail_content) or die "";
    }
} 

sub senderror {
    if (-e $resultfile) {
        my $file_return = tie my @log_array, "Tie::File", $resultfile;
        if ($file_return) {
            my $log_lines = @log_array;
            if ($log_lines > 0) {
                my @mail_body;
                for(my $i = 1;$i <= $log_lines ; $i++) {
                    push(@mail_body,$log_array[$i-1]);
                }
                my $mail_subject = "Prod error messenge";
                my $mail_content = join("\n",@mail_body);
                my $sender = Wenwen::Email->new();
                $sender->send($mail_to,$mail_subject,$mail_content) or die "";
            }
        }
    }
}

sub main {
    &config
    &filter($prod_full_file)
    &senderror
}

&main
