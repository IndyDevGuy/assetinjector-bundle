<?php
namespace IndyDevGuy\AssetInjectorBundle\Modal\Asset;

use Symfony\Component\Asset\PackageInterface;
use Twig\Environment;

class TwigAssetPackage implements PackageInterface
{
    private $twig;
    private $data;

    public function __construct(Environment $twig,array $data)
    {
        $this->twig = $twig;
        $this->data = $data;
    }

    /**
     * @inheritDoc
     */
    public function getVersion($path)
    {
        // TODO: Implement getVersion() method.
    }

    /**
     * @inheritDoc
     */
    public function getUrl($path)
    {
        return $this->twig->render($path,$this->data);
    }

    public function getData()
    {
        return $this->data;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }
}