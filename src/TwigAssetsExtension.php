<?php

namespace Butterfly\Component\TwigAssetsExtension;

/**
 * @author Marat Fakhertdinov <marat.fakhertdinov@gmail.com>
 */
class TwigAssetsExtension extends \Twig_Extension
{
    /**
     * @var string
     */
    protected $manifestFilepath;

    /**
     * @var string
     */
    protected $assetPrefix;

    /**
     * @var array
     */
    protected $manifestData = null;

    /**
     * @param string $manifestFilepath
     * @param string $assetPrefix
     */
    public function __construct($manifestFilepath, $assetPrefix)
    {
        $this->manifestFilepath = $manifestFilepath;
        $this->assetPrefix      = $assetPrefix;
    }

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('basset', [$this, 'getAsset']),
        ];
    }

    /**
     * @param string $asset
     * @return string
     */
    public function getAsset($asset)
    {
        if (null === $this->manifestData) {
            $this->manifestData = $this->loadManifestData($this->manifestFilepath);
        }

        $key = $this->assetPrefix . $asset;

        return array_key_exists($key, $this->manifestData) ? $this->manifestData[$key] : $asset;
    }

    /**
     * @param string $manifestFilepath
     * @return array
     */
    protected function loadManifestData($manifestFilepath)
    {
        if (!is_readable($manifestFilepath)) {
            throw new \InvalidArgumentException(sprintf('Manifest file %s is not found', $manifestFilepath));
        }

        $result = json_decode(file_get_contents($manifestFilepath), true);

        if (false === $result) {
            throw new \InvalidArgumentException(sprintf('Incorrect manifest file %s', $manifestFilepath));
        }

        return $result;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'butterfly_webpack_assets.extension';
    }
}
