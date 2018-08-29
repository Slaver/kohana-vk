# Auth module for Open API vk.com

[Open API](http://vkontakte.ru/club1) is the system for developers of third-party sites, which enables them to authenticate users of vk.com on their websites.

## Installation

First, add the submodule to your Git application:

    git submodule add git://github.com/Slaver/vk.git modules/vk
    git submodule update --init

Or clone the module separately:

    cd modules
    git clone git://github.com/Slaver/vk.git vk

### Update module

    cd modules/vk
    git submodule update --init

### Configuration

Edit `application/bootstrap.php` and add the module:

    Kohana::modules(array(
        ...
        'vk' => MODPATH.'vk',
        ...
    ));

## Usage

### Get your API ID and password

Create new API at [vk.com](http://vkontakte.ru/apps.php?act=add).
[Get unique API ID, password and secret key](http://vkontakte.ru/apps.php#act=admin) and put it in `config/vk.php`.

### Create xd_receiver.htm

Create file xd_receiver.htm in the root directory of your site. VK authentication doesn't work without this file

    <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
       "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="http://www.w3.org/1999/xhtml">
      <head>
        <title>Open API XD Receiver</title>
      </head>
      <body>
        <script src="http://vkontakte.ru/js/api/xd_receiver.js" type="text/javascript"></script>
      </body>
    </html>

### Controller and actions

See `classes/controller/vk.php`
