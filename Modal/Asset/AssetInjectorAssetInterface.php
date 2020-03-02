<?php
namespace IndyDevGuy\AssetInjectorBundle\Modal\Asset;

use Symfony\Component\Asset\PackageInterface;

interface AssetInjectorAssetInterface
{
    public function __construct(string $name, string $version,int $priority,string $location,string $type,string $path);
    public function getName():string;
    public function setName(string $name);
    public function getType():string;
    public function setType(string $type);
    public function getPath():string;
    public function setPath(string $path);
    public function getPRiority():int;
    public function setPriority(int $priority);
    public function getPackage():PackageInterface;
    public function setPackage(PackageInterface $package);
    public function getRendered():bool;
    public function setRendered(bool $rendered);
    public function getVersion():string;
    public function setVersion(string $version);
    public function getLocation():string;
    public function setLocation(string $location);
    public function getRenderData():string;
    public function render():bool;
}