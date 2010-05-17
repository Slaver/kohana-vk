# Auth module for Open API vk.com

Open API is the system for developers of third-party sites, which enables them to authenticate users of vk.com on their websites.

http://vkontakte.ru/club1

## Installation

First, add the submodule to your Git application:

    git submodule add git://github.com/Slaver/vk.git modules/vk
    git submodule update --init

Or clone the the module separately:

    cd modules
    git clone git://github.com/Slaver/vk.git vk

### Update module

    cd modules/vk
    git submodule update --init

### Configuration

Edit `application/bootstrap.php` and add a the module:

    Kohana::modules(array(
        ...
        'vk' => MODPATH.'vk',
        ...
    ));

## Usage

See `classes/controller/vk.php`
