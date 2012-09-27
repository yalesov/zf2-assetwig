<?php
namespace Heartsentwined\Assetwig\Assetic;

use Assetic\AssetWriter as BaseAssetWriter;
use Assetic\AssetManager;
use Heartsentwined\ArgValidator\ArgValidator;

class AssetWriter extends BaseAssetWriter
{
    protected $dir;

    public function __construct($dir)
    {
        ArgValidator::assert($dir, 'string');
        $this->dir = $dir;
        parent::__construct($dir);
    }

    public function writeManagerAssets(AssetManager $am)
    {
        foreach ($am->getNames() as $name) {
            $asset = $am->get($name);
            $path = $this->dir . '/' . $asset->getTargetPath();
            if (!file_exists($path)
                || filemtime($path) < $asset->getLastModified()) {
                $this->writeAsset($asset);
            }
        }
    }
}
