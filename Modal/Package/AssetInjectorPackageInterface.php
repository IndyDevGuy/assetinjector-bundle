<?php
namespace IndyDevGuy\AssetInjectorBundle\Modal\Package;

use IndyDevGuy\AssetInjectorBundle\Modal\Asset\AssetInjectorAssetInterface;
use Doctrine\Common\Collections\ArrayCollection;

interface AssetInjectorPackageInterface
{
    public function __construct(string $name,string $version,int $priority);
    public function getName(): string;
    public function setName(string $name);
    public function getPriority(): int;
    public function setPriority(int $priority);
    public function setVersion(string $version);
    public function getVersion(): string;
    public function getRendered(): bool;
    public function getAssets(): ArrayCollection;
    public function addAsset(AssetInjectorAssetInterface $asset);
    public function removeAsset(AssetInjectorAssetInterface $asset);
    public function getAssetCount(): int;
    public function render():bool;
    public function getBeforeRenderData():array;
    public function getAfterRenderData():array;
    public function orderAssets();

}