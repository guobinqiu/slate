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

1. setup your directory

    ```bash
    $ mkdir -p /data/src/$USER/
    $ cd !$
    $ git clone git@github.com:voyagechinagroup/PointMedia.git ./
    $ make setup
    ```

1. configure yml files in `./app/config/`

    * `./app/config/parameters.yml`
      * DB configuration
      * for `vagrant` environment, password should be emtpy
    * `app/config/custom_parameters.yml`
      * `webuser_signup.login.username`
      * `webuser_signup.login.password`
      * `webpower.login.username`
      * `webpower.login.password`
      * `signup.crypt_method`
      * `signup.salt`
      * `recruit_offerwow`
      * `recruit_offer99`


1. setup /etc/hosts on your **local**  machine.

    ```
    115.29.208.157 $USER.www.91jili.com.dev.91jili.com
    ```

## Document root

| site           | URL                                    |
|----------------|----------------------------------------|
| 91jili         | http://$USER.www.91jili.com.dev.91jili.com/app\_dev.php |


## Tips

### When you need 2nd URL for test.

1. set up your 2nd directory

    ```bash
    $ mkdir -p /data/src/seki-2/
    $ cd !$
    $ git clone git@github.com:voyagechinagroup/PointMedia.git ./
    $ make setup SUBDOMAIN=seki-2
    ```

1. configure DB `./app/config/parameters.yml`

1. setup /etc/hosts on your **local**  machine.

    ```
    115.29.208.157 seki-2.www.91jili.com.dev.91jili.com
    ```
