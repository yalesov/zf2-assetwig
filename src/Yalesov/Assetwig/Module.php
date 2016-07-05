<?php
namespace Yalesov\Assetwig;

use Yalesov\Yaml\Yaml;

class Module
{
    public function getConfig()
    {
        return Yaml::parse(__DIR__ . '/../../../config/module.config.yml');
    }
}
