# check-php-mbstring-by-yahoo

## Requirements

- php >= 5.3
- jq
- Yahoo Japan Developer Application ID

## Setup

```
$ git clone https://github.com/sadapon2008/check-php-mbstring-by-yahoo.git
$ cd check-php-mbstring-by-yahoo
$ curl -sS https://getcomposer.org/installer | php
$ php composer.phar install
```

## Example

```
$ env YAHOO_APPID=xxxxxxxx sh run_check.sh /path/to/php_src_dir/
$ cat result.json
```
