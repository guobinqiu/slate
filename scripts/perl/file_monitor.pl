#!/usr/bin/perl
use strict;
use warnings;
use Tie::File;
use Fcntl 'O_RDONLY';
use Net::SMTP;
use Authen::SASL;
use Log::Lite qw(logrotate logmode logpath log);
use YAML::XS;
use Lib::SMTP;

########################配置信息#######################################
#设置log文件的存放目录
logpath("./logs");

#读取smtp的基本信息
my $config=eval{YAML::XS::LoadFile('./config/file_monitor.yaml')};
if (defined($config)) {
            my $mail_to=$config->{mail}->{to};
            #获取监控进程的数目
            my $process_num=`ps -ef|grep inotifywait|grep -v grep|wc -l`;
            if ($process_num == 1) {
                        #系统内监控进程数为1的时候#
                        #打开log文件
                        my $file_return=tie my @log_array,"Tie::File","/data/log.txt";
                        if ($file_return) {
                                    my $log_lines=@log_array;
                                    #判断log文件内是否有内容
                                    if ($log_lines > 0) {
                                                #行数大于0的时候,发送邮件.
                                                my @mail_body;
                                                for(my $i=1;$i<=$log_lines;$i++){
                                                            push(@mail_body,$log_array[$i-1]);
                                                }
                                                my $mail_title="文件已发生变更,请检查";
                                                my $mail_content=join("\n",@mail_body);
                                                my $mail_return=sendmail($mail_to,$mail_title,$mail_content);
                                                if ($mail_return == 1) {
                                                            log("monitor_log","[文件以改变]邮件发送成功");
                                                }else{
                                                            log("monitor_error","[文件以改变]邮件发送失败,错误代码是".$mail_return);
                                                            exit;
                                                }
                                                my $clean_retun=`cat /dev/null > /data/log.txt`;
                                                if (!$clean_retun) {
                                                            log("monitor_log","log文件已成功清空");
                                                }else{
                                                            my $clean_mail_title="清空log文件失败";
                                                            my $clean_mail_subject="清空log文件失败,请检查服务器状况";
                                                            my $clean_mail_return=sendmail($mail_to,$clean_mail_title,$clean_mail_subject);
                                                            #判断函数sendmail是否执行成功
                                                            if ($clean_mail_return == 1) {
                                                                        log("monitor_log","[清空log文件失败]邮件发送成功");
                                                            }else{
                                                                        log("monitor_error","[清空log文件失败]邮件发送失败,错误代码是".$mail_return);
                                                            }

                                                }
                                    }else{
                                                #监控文件没有变化
                                                log("monitor_log","监控文件没有变化");
                                    }
                        }else{
                                    #打开log文件失败
                                    log("monitor_error","打开log文件失败");
                                    ###打开log文件失败也可以发邮件###
                                    my $mail_title="[监视文件]log文件打开失败";
                                    my $mail_content="监视文件perl脚本无法打开log文件,请检查问题";
                                    my $mail_return=sendmail($mail_to,$mail_title,$mail_content);
                                    if ($mail_return == 1) {
                                                log("monitor_log","[无法打开log文件]邮件发送成功");
                                    }else{
                                                log("monitor_error","[无法打开log文件]邮件发送失败");
                                    }


                        }
            }elsif($process_num < 1){
                        #监控进程不存在的时候
                        my $mail_title="监控进程不存在";
                        my $mail_body="检测到监控进程不存下,请检查";
                        my $mail_return=sendmail($mail_to,$mail_title,$mail_body);
                        if ($mail_return == 1) {
                                    log("monitor_log","[监控进程不存在]邮件发送成功");
                        }else{
                                    log("monitor_error","[监控进程不存在]邮件发送失败,错误代码是".$mail_return);
                        }

            }else{
                        #监控进程数大于1的时候
                        my $mail_title="系统监控进程大于1";
                        my $mail_body="系统内监控进程数异常,请立马检查系统情况";
                        my $mail_return=sendmail($mail_to,$mail_title,$mail_body);
                        if ($mail_return == 1) {
                                    log("monitor_log","[监控进程数异常]邮件发送成功");
                        }else{
                                    log("monitor_error","[监控进程数异常]邮件发送失败,错误代码是".$mail_return);
                        }
            }
}else{
            log("monitor_error","无法打开YAML配置文件");
}
