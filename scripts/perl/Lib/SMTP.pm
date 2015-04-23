#!/usr/bin/perl
package Lib::SMTP;
    use strict;
    use warnings;
    use Net::SMTP;
    use Authen::SASL;
    use YAML::XS;

    BEGIN {
        require Exporter;
        our $VERSION     = 1.00;
        our @ISA         = qw(Exporter);
        our @EXPORT      = qw(sendmail);
    }
    ###可以再函数里面读取smtp配置文件###
    #发邮件的函数
    sub sendmail{
        #sendmail返回代码解释
        #1 -> 函数正确执行完毕
        #2 -> 无法连接smtp服务器
        #3 -> smtp验证信息失败
        #4 -> 传入的参数错误
        #5 -> 邮件发送失败
        #6 -> 打开yaml配置文件失败
        my $open_file=eval{YAML::XS::LoadFile('./Lib/SMTP.yaml')};
        #判断yaml配置文件是否正常打开
        if (defined($open_file)) {
            #yaml配置文件正常打开
            my $mail_host=$open_file->{smtp}->{server};
            my $mail_username=$open_file->{smtp}->{username};
            my $mail_password=$open_file->{smtp}->{password};

            my ($mail_to,$subject,$mail_text)=@_;
            if (!defined($mail_to) or !defined($subject) or !defined($mail_text)) {
                #传入的参数不正确
                return 4;
            }else{
                #并判断smtp是否连接成功
                my $smtp;
                my $smtp_rtn=eval{$smtp=Net::SMTP->new($mail_host,Timeout => 120)};
                if (defined($smtp_rtn)) {
                    #连接smtp服务器成功
                    $smtp->auth($mail_username,$mail_password);
                    my @mail_to=split(/,/,$mail_to);
                    foreach my $mails_to(@mail_to){
                    #判断连接smtp验证信息
                    if($smtp->to($mails_to)){
                        #smtp验证成功
                        $smtp->data();
                        $smtp->datasend("To: $mails_to\n");
                        $smtp->datasend("From: $mail_username\n");
                        $smtp->datasend("Subject: $subject\n");
                        $smtp->datasend("\n");
                        $smtp->datasend("$mail_text\n");
                        my $send_return=eval{$smtp->dataend()};
                        if (!defined($send_return)) {
                            #邮件发送失败
                            return 5;
                        }
                            }else{
                            #smtp验证失败
                            return 3;
                            }
                        }
                            #邮件发送成功
                            return 1;
                    }else{
                        #无法连接smtp服务器
                        return 2;
                    }
                }
        }else{
            #打开yaml配置文件失败
            return 6;
        }
        }

1;