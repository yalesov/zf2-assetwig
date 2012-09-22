# zf2-assetwig

[![Build Status](https://secure.travis-ci.org/heartsentwined/zf2-assetwig.png)](http://travis-ci.org/heartsentwined/zf2-assetwig)

This module integrates [Assetic](https://github.com/kriswallsmith/assetic) and [Twig](http://twig.sensiolabs.org/) to a ZF2 appliation.

# Installation

[Composer](http://getcomposer.org/):

`"minimum-stability": "dev"` is required because `twig/extensions` is still in dev stage.

```json
{
    "minimum-stability": "dev",
    "require": {
        "heartsentwined/zf2-assetwig": "2.*"
    }
}
```

Then add `Heartsentwined\Assetwig` to the `modules` key in `(app root)/config/application.config.*`

# Config

Copy `config/assetwig.local.php.dist` to `(app root)/config/autoload/assetwig.local.php`, and modify the settings. Instructions included in config file.

# Usage

todo
