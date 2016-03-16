# PointMedia

## How to setup

if USER = seki

### Prerequirement

1. SSH account for testgroup.91jili.com machine
2. github account
3. ssh-key registration on github https://github.com/settings/ssh
4. git author setting

    ```bash
    $ git config --global user.name "Takafumi Sekiguchi"
    $ git config --global user.email "takafumi.sekiguchi@d8aspring.com"
    ```

### Setup

1. setup umask

    ```bash
    $ echo "umask 0002" >> ~/.bashrc
    ```

1. Configure `/etc/httpd/conf.d/$USER.conf`

    ```conf
     #Listen 444

     # get the server name from the Host: header
     UseCanonicalName Off

     # splittable logs
     LogFormat "%{Host}i %h %l %u %t \"%r\" %s %b" vcommon

     <VirtualHost *:80>
         ServerName  p.$USER.testgroup.91jili.com
         ServerAlias *.$USER.testgroup.91jili.com
         VirtualDocumentRoot /data/web/{developer_sub_directory}/%1/web
         RewriteEngine On
         RewriteCond %{REQUEST_METHOD} !^(GET|POST|HEAD)$
         RewriteRule .* - [F]
          ErrorLog  /var/log/httpd/$USER.testgroup.com_error.log
          CustomLog  /var/log/httpd/$USER.testgroup.com_access.log  vcommon
     </VirtualHost>

     <Directory /data/web/$USER/web/*/web>
         AllowOverride All
     </Directory>

     <VirtualHost *:443>
         ServerName  p.$USER.testgroup.91jili.com
         ServerAlias *.$USER.testgroup.91jili.com
         VirtualDocumentRoot /data/web/{developer_sub_directory}/%1/web
          ErrorLog  /var/log/httpd/$USER.testgroup.com_ssl_error.log
          TransferLog /var/log/httpd/$USER.testgroup.com_ssl_access.log
     LogLevel warn
     SSLEngine on
     SSLProtocol all -SSLv2
     SSLCipherSuite ALL:!ADH:!EXPORT:!SSLv2:RC4+RSA:+HIGH:+MEDIUM:+LOW
     SSLCertificateFile /etc/pki/tls/certs/jiangtao.crt
     SSLCertificateKeyFile /etc/pki/tls/private/jiangtao.key

     <Files ~ "\.(cgi|shtml|phtml|php3?)$">
         SSLOptions +StdEnvVars
     </Files>
     <Directory "/var/www/cgi-bin">
         SSLOptions +StdEnvVars
     </Directory>
     SetEnvIf User-Agent ".*MSIE.*" \
              nokeepalive ssl-unclean-shutdown \
              downgrade-1.0 force-response-1.0
     CustomLog logs/ssl_request_log \
               "%t %h %{SSL_PROTOCOL}x %{SSL_CIPHER}x \"%r\" %b"
     </VirtualHost>
    ```

1. setup document roots

    ```bash
    $ mkdir -p /data/web/$USER/1
    $ cd !$
    $ git clone git@github.com:voyagechinagroup/PointMedia.git ./
    $ make setup
    ```

1. configure DB `/data/web/$USER/1/app/config/parameters.yml`

## Document root

| site           | URL                                 |
|----------------|-------------------------------------|
| 91jili         | http://1.$USER.testgroup.91jili.com |


