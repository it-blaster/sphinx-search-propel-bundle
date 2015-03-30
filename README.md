SphinxSearchPropelBundle
====================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/it-blaster/sphinx-search-propel-bundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/sphinx-search-propel-bundle/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/it-blaster/sphinx-search-propel-bundle/badges/build.png?b=master)](https://scrutinizer-ci.com/g/it-blaster/sphinx-search-propel-bundle/build-status/master)

Symfony2. Sphinx search this site with the use Propel ORM

Installation
------------

Добавьте <b>ItBlasterSphinxSearchPropelBundle</b> в `composer.json`:

```js
{
    "require": {
        "it-blaster/sphinx-search-propel-bundle": "dev-master"
	},
}
```

Теперь запустите композер, чтобы скачать бандл командой:

``` bash
$ php composer.phar update it-blaster/sphinx-search-propel-bundle
```

Композер установит бандл в папку проекта `vendor/it-blaster/sphinx-search-propel-bundle`.

Далее подключите бандл в ядре `AppKernel.php`:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new ItBlaster\SphinxSearchPropelBundle\ItBlasterSphinxSearchPropelBundle(),
    );
}
```

В файл `app/config/config.yml` добавьте настройки бандла:
``` yml
it_blaster_sphinx_search_propel:
     searchd:
         # Хост для подключения к демону Sphinxc
         host: localhost
         # Порт для подключения к демону Sphinx
         port: %sphinx_port%
         # Файл сокета, если нужно подключаться к демону через сокет
         #socket: /path/to/socket.file
     indexes:
         # Список индексов Sphinx (ключ) и имен Entity (значение)
         # которые будут использоваться при поиске
        newsIndex: "App\MyBundle\Model\\NewsQuery"
```
В секции `indexes` будут перечислены все сущности, по которым будет осуществляться поиск.

Скопируйте из папки бандла `ap/config` файл `sphinx.conf.dist` в папку проекта `app/config/sphinx.conf` и пропишите нужные конфиги для индексов. В файле `sphinx.conf.dist` прописаны конфиги индексов для сущности Новости.

Usage
------------
Добавьте в `.gitigmore` папку `app/data/*`, в ней будут храниться индексы sphinx'а. Сам sphinx нужные ему папки создать не может, поэтому создаём их руками:
`mkdir app/data && mkdir app/data/sphinx && mkdir app/data/sphinx/indexes && mkdir app/logs/sphinx && chmod -R 777 app/data app/logs/`

Команды для работы с sphinx'ом:
1. Проиндексировать: indexer --config app/config/sphinx.conf --All --rotate
2. Запустить поиск: searchd -c app/config/sphinx.conf

`app/config/sphinx.conf` - путь до файла с конфигом sphinx'а

Вначале выполняем 1ую команду. Если всё ок (т.е. нет ошибок в консоли), выполняем 2ую.