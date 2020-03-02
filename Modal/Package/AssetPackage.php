<?php
namespace IndyDevGuy\AssetInjectorBundle\Modal\Package;

use IndyDevGuy\AssetInjectorBundle\Modal\Asset\AssetInjectorAssetInterface;
use Doctrine\Common\Collections\ArrayCollection;

class AssetPackage implements AssetInjectorPackageInterface
{
    private $name;
    private $priority;
    private $rendered;
    private $assets;
    private $beforeRenderData;
    private $afterRenderData;
    private $version;

    public function __construct(string $name, string $version, int $priority)
    {
        $this->assets = new ArrayCollection();
        $this->beforeRenderData = array();
        $this->afterRenderData = array();
        $this->rendered = false;
        $this->name = $name;
        $this->version = $version;
        $this->priority = $priority;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getPriority(): int
    {
        return $this->priority;
    }

    public function setPriority(int $priority)
    {
        $this->priority = $priority;
    }

    public function getRendered(): bool
    {
        return $this->rendered;
    }

    public function getAssets(): ArrayCollection
    {
        return $this->assets;
    }

    public function addAsset(AssetInjectorAssetInterface $asset)
    {
        if(!$this->assets->contains($asset))
        {
            $this->assets->add($asset);
            //var_dump('Added Asset '.$asset->getName().' to package '. $this->getName());
        }
    }

    public function removeAsset(AssetInjectorAssetInterface $asset)
    {
        if($this->assets->contains($asset))
        {
            $this->assets->removeElement($asset);
        }
    }

    public function getAssetCount(): int
    {
        return $this->assets->count();
    }

    public function render(): bool
    {
        foreach($this->assets as $asset)
        {
            if($asset instanceof AssetInjectorAssetInterface)
            {
                if($asset->getRendered() == false)
                {
                    //var_dump('calling render on asset');
                    $assetRendered = $asset->render();
                    if($assetRendered == true) {
                        //var_dump('asset '.$asset->getName() . ' array being created');
                        $tempArray = array(
                            'type'=>$asset->getType(),
                            'data'=>$asset->getRenderData(),
                            'name'=>$asset->getName(),
                            'package'=>$this->name,
                            'packageVersion'=>$this->version,
                            'path'=>$asset->getPath(),
                            'version'=>$asset->getVersion(),
                            'displayed'=>false,
                        );
                        if($asset->getLocation() == 'before') {
                            $this->beforeRenderData[] = $tempArray;
                            //var_dump('Asset '.$asset->getName() . ' being added to before array');
                            //var_dump($this->beforeRenderData);
                        } elseif($asset->getLocation() == 'after') {
                            $this->afterRenderData[] = $tempArray;
                            //var_dump('Asset '.$asset->getName() . ' being added to after array');
                            //var_dump($this->afterRenderData);
                        }
                    }
                }
            }
        }
        $this->rendered = true;
        return $this->rendered;
    }

    public function getBeforeRenderData(): array
    {
        return $this->beforeRenderData;
    }

    public function getAfterRenderData(): array
    {
        return $this->afterRenderData;
    }

    public function orderAssets()
    {
        $iterator = $this->assets->getIterator();
        $iterator->uasort(function ($first, $second) {
            if ($first === $second) {
                return 0;
            }
            return $first->getPriority() > $second->getPriority() ? -1 : 1;
        });
        $this->assets = new ArrayCollection(iterator_to_array($iterator));
    }

    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    public function getVersion(): string
    {
        return $this->version;
    }
}