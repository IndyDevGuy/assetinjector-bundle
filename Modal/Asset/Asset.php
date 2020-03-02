<?php
namespace IndyDevGuy\AssetInjectorBundle\Modal\Asset;

use App\AssetInjector\Modal\Package\TwigAsset;
use Symfony\Component\Asset\PackageInterface;

class Asset implements AssetInjectorAssetInterface
{
    public $name;
    public $version;
    public $type;
    public $path;
    public $location;
    public $priority;
    public $rendered = false;
    public $package;
    public $renderData;

    public function __construct(string $name, string $version, int $priority, string $location, string $type, string $path)
    {
        $this->name = $name;
        $this->version = $version;
        $this->priority = $priority;
        $this->location = $location;
        $this->type = $type;
        $this->path = $path;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPackage(): PackageInterface
    {
        return $this->package;
    }

    public function setPackage(PackageInterface $package)
    {
        $this->package = $package;
    }

    public function getRendered(): bool
    {
        return $this->rendered;
    }

    public function setRendered(bool $rendered)
    {
        $this->rendered = $rendered;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    public function getRenderData(): string
    {
        return $this->renderData;
    }

    public function render():bool
    {
        if(!$this->rendered) {
            //var_dump('rendering asset' . $this->name);
            if (isset($this->package) && $this->package instanceof PackageInterface) {
                $this->renderData = $this->package->getUrl($this->path);
                $this->rendered = true;
            }
        }
        return $this->rendered;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location)
    {
        $this->location = $location;
    }

    public function getPRiority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority)
    {
        $this->priority = $priority;
    }

}