#!/usr/bin/perl
use strict;
use warnings;
use YAML::XS;
use Lib::SMTP;
use Log::Lite qw(logrotate logmode logpath log);
use Time::Piece;

logpath("./logs");
my $open_file=eval{YAML::XS::LoadFile('./config/log_bk.yaml')};

if (defined($open_file)) {
    #打开yaml配置文件成功
    my $host_name=`/bin/hostname`;
    my $mail_to=$open_file->{mail_to}->{to};
    my $ts = localtime->strftime("%Y-%m-%d");
        my $sys_dir=$open_file->{dir};
        my %dir_hash=%{$sys_dir};
        my $log_keywords=$open_file->{keywords};
        my %key_hash=%{$log_keywords};
        my $mkdir_result=system("/bin/mkdir /data/log_backup/$ts");
        if ($mkdir_result == 0) {
            #创建备份目录成功
            my @file_list;
            my $num=0;
            my $file_num=0;
            while (my ($k,$v)=each(%dir_hash)) {
                #循环cp日志文件到备份目录
                my @key_array=split(/,/,$key_hash{$k});
                $file_num=$file_num+@key_array;
                ###应当先判断目录是否存在###
                if (-e $v) {
                    foreach my $key_value(@key_array){
                        my $result=`/bin/ls -rt $v|grep -w $key_value|head -n 1`;
                        if ($? == 0) {
                            chomp($result);
                            my $cp_result=system("/bin/cp $v/$result /data/log_backup/$ts/");

                            ###将cp的列表发送邮件###
                            if ($cp_result != 0) {
                                #拷贝日志文件失败,记录日志.
                                log("error_log_bk","拷贝文件失败,请检查文件权限");
                                ###出错的时候不应该退出###
                            }else{
                                push(@file_list,$v.'/'.$result);
                                $num=$num+1;
                            }
                        }else{
                            log("error_log_bk","ls命令没有执行成功,请检查目录或文件权限");
                        }
                    }
                }else{
                    log("error_log_bk","$v 目录不存在,请检查");
                }
            }
            #压缩日志文件
            chomp($host_name);
            my $tar_result=system("/bin/tar -zcf /data/log_backup/log_backup_${ts}_$host_name.tar.gz /data/log_backup/$ts/ --remove-files");
            if ($tar_result != 0) {
                #tar压缩失败,记录日志
                log("error_log_bk","系统执行tar命令失败,请检查目录权限");
            }else{
                    #传输文件成功,发送邮件
                    my $mail_title="Log文件成功备份".$host_name;
                    my $file_body=join("\n",@file_list);
                    my $sendmail_return;
                    if ($file_num == $num) {
                        #日志备份成功
                        my $mail_subject="本周备份log文件perl脚本已经成功执行完成\n备份的文件为:\n $file_body";
                        $sendmail_return=sendmail($mail_to,$mail_title,$mail_subject);
                    }else{
                        #日志备份有错误发生
                        my $mail_subject="本周备份log文件perl脚本有错误发生\n备份的文件为:\n $file_body";
                        $sendmail_return=sendmail($mail_to,$mail_title,$mail_subject);
                    }
                    if ($sendmail_return != 1) {
                        #邮件发送失败,记录错误代码
                        log("error_log_bk","log备份通知邮件没有发送成功,错误代码是 $sendmail_return");
                    }

            }
        }else{
            #常见备份目录失败
            log("error_log_bk","执行mkdir命令失败,请检查目录权限");
        }
}else{
    #打开yaml配置文件失败
    log("error_log_bk","无法打开配置文件,请检查文件是否存在或目录权限");
}

