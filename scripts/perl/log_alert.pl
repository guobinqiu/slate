#!/usr/bin/perl
use strict;
use warnings;
#use LWP::Simple qw(get);
use Digest::MD5 qw(md5 md5_hex md5_base64);
#use JSON;
use Net::SMTP;
use Authen::SASL;
use YAML::XS;
use Lib::SMTP;
use Log::Lite qw(logrotate logmode logpath log);
# use FindBin qw($Bin);

#定义log文件目录
logpath("./logs");

# print $Bin;

log("log_alert", "log_alert started.");

my $config_return = eval{YAML::XS::LoadFile('./config/log_alert.yaml')};
if (!defined($config_return)) {
    log("log_alert","[error] ./config/log_alert.yaml is not read correctly!");
    die("[error] ./config/log_alert.yaml is not read correctly!");
}

my $mail_to = $config_return->{mail}->{to};

my $log_file;

my $mail_content;
foreach $log_file (@{$config_return->{log_files}}){
    print "$log_file \n";
        my $head = "====start of $log_file====\n";
        my $bottom = "====end   of $log_file====\n";
    if(open(FH, $log_file)){
        my @linelist = <FH>;
        my $content = join("",@linelist);
        $mail_content .= $head;
        $mail_content .= $content;
        $mail_content .= $bottom;
        $mail_content .= "\n";
    #    print $mail_content;
    }
    else{
    }  

}

my $mail_subject="Error logs of jili_web related to httpd. ";
my $send_return=sendmail($mail_to,$mail_subject,$mail_content);

log("log_alert", "log_alert ended.");
