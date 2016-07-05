# zf2-assetwig

[![Build Status](https://secure.travis-ci.org/yalesov/zf2-assetwig.png)](http://travis-ci.org/yalesov/zf2-assetwig)

This module integrates [Assetic](https://github.com/kriswallsmith/assetic) and [Twig](http://twig.sensiolabs.org/) to a ZF2 appliation.

# Installation

[Composer](http://getcomposer.org/):

`"minimum-stability": "dev"` is required because `twig/extensions` is still in dev stage.

```json
{
    "minimum-stability": "dev",
    "require": {
        "yalesov/zf2-assetwig": "3.*"
    }
}
```

Then add `Yalesov\Assetwig` to the `modules` key in `(app root)/config/application.config.*`

# Config

Copy `config/assetwig.local.php.dist` to `(app root)/config/autoload/assetwig.local.php`, and modify the settings. Instructions included in config file.

# Usage

todo
