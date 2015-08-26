# ftp-adm-frontend

 Welcome to **FTP ADM Frontend**




## Install

First, clone the repository, and in the root directory :

```
$php app/check.py
```

Follow instruction, but main error is to install composer and launch :

```
$ curl -sS https://getcomposer.org/installer | php
[...]
$ php composer.phar install
[...]
$ php composer.phar update
```

Install MySQL DB with init data :
```
$ mysql -u root -p YOURDB < install/qlowd.sql
$ mysql -u root -p YOURDB < install/qlowd-INIT-DATA.sql # Generate first company & user
```

And to launch app in dev mode :

```
$php app/console server:run
```

## Defautl Credentials

* Login: qlowd
* MdP: qlowd

Secure :)
