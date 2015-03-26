SphinxSearchPropelBundle
====================

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