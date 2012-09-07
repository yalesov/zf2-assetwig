# zf2-assetwig

[![Build Status](https://secure.travis-ci.org/heartsentwined/zf2-assetwig.png)](http://travis-ci.org/heartsentwined/zf2-assetwig)

This module integrates [Assetic](https://github.com/kriswallsmith/assetic) and [Twig](http://twig.sensiolabs.org/) to a ZF2 appliation.

# Installation

Use composer.

```json
{
    "require": {
        "heartsentwined/zf2-assetwig": "1.*"
    }
}
```

Then add `Assetwig` to the `modules` key in `(app root)/config/application.config.yml`

# Config

Copy `config/assetwig.local.php.dist` to `(app root)/config/autoload/assetwig.local.php`, and modify the settings. Instructions included in config file.
