# MSISDN

## DESCRIPTION
A lightweight PHP library which resolves mobile network operator, country data and subscriber number from MSISDN.

## Synopsis
Library that returns MSISDN information
- takes MSISDN as an input
- returns MNO identifier, country dialling code, subscriber number and country identifier as defined with ISO 3166-1-alpha-2
- library is exposed via REST API using Slim framework ([official site](https://www.slimframework.com/) and  [git repository](https://github.com/slimphp/Slim)) 

Requirements
-------------------
* PHP 7.1 or higher

## Installation

### Requirements:
- Git
- Composer

### Steps:
1. Git
```
git clone ssh://git@github.com/mzzz47/msisdn 
```
2. cd msisdn

3. run install.sh which takose care of composer and creates log folder and files
```
./install.sh
```

4. Web server setup for Slim is described [here](https://www.slimframework.com/docs/v3/start/web-servers.html)
Nginx example :
```
server {
    listen 80;
    server_name msisdn-ftw.net;
    
    #logs
    error_log /path/to/msisdn/logs/error.log;
    access_log /path/to/msisdn/logs/access.log;
    
    root /path/to/msisdn/api;
    index index.php;
    
    location / {
      try_files $uri /index.php$is_args$args;
    }

    location ~ \.php {
      try_files $uri =404;
      
      fastcgi_split_path_info ^(.+\.php)(/.+)$;
      
      include fastcgi_params;
      fastcgi_param           SCRIPT_FILENAME $document_root$fastcgi_script_name;
      fastcgi_param           SCRIPT_NAME $fastcgi_script_name;
      fastcgi_index           index.php;
      # IP
      fastcgi_pass            127.0.0.1:9000;
      # Sockets (check the path to the socket file)
      #fastcgi_pass           unix:/var/run/php-fpm/php-fpm.sock;
    }
}
```

## Usage
### GET:
```
url: http://msisdn-ftw.net/v1/msisdn/447700900663
```

### POST:
```
url: http://msisdn-ftw.net/v1/msisdn/
parameter:
{
  msisdn: '+44 7700 900663'
}
```

### Response json:
```
{
  "CountryISO": "GB",
  "CountryPrefix": "44",
  "MobileNetworkCode": "77",
  "ProviderName": "BT Group",
  "SubscriberNumber": "00900663"
}
```

## Tests
Composer will setup the test environent for you. Run tests using:
```sh
./vendor/bin/phpunit --bootstrap vendor/autoload.php tests
```
from within a project root folder

## Author
Matej Županić
