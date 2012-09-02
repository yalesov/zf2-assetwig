<?php
namespace Assetwig\Assetic;

use Assetic\AssetWriter as BaseAssetWriter;
use Assetic\AssetManager;
use Assetic\Util\PathUtils;

class AssetWriter extends BaseAssetWriter
{
    protected $dir;
    protected $varValues;

    public function __construct($dir, array $varValues = array())
    {
        $this->dir = $dir;
        parent::__construct($dir, $varValues);
    }

    public function writeManagerAssets(AssetManager $am)
    {
        foreach ($am->getNames() as $name) {
            $asset = $am->get($name);
            $targetPath = $this->dir.'/'.PathUtils::resolvePath(
                $asset->getTargetPath(), $asset->getVars(), $asset->getValues());
            if (!file_exists($targetPath)
                || filemtime($targetPath) < $asset->getLastModified()) {
                $this->writeAsset($asset);
            }
        }
    }
}
