## Symfony 4 AssetInjector

###What is it and how does it work?

Well its a manager... of assets hehe no seriously it manages assets like css, js and even dynamic twig templates. 

Installation
============

### Step 1: Download the Bundle

Open a command console, enter your project directory and execute the
following command to download the latest stable version of this bundle:

```console
$ composer require indydevguy/assetinjector-bundle
```

This command requires you to have Composer installed globally, as explained
in the [installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Step 2: Enable the Bundle

If you are using Symfony Flex this should be done for you.

If you are not using Symfony Flex then you have to enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:
```php
<?php
// config/bundles.php

return [
    // ...
    IndyDevGuy\AssetinjectorBundle\AssetInjectorBundle::class => ['all' => true],
    // ...
];
```

###Installation Complete


