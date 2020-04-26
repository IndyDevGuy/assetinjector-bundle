<?php
namespace IndyDevGuy\AssetInjectorBundle\DataCollector;

use IndyDevGuy\AssetInjectorBundle\Service\AssetInjector;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Throwable;

class AssetInjectorCollector extends DataCollector
{
    private $assetInjector;

    public function __construct(AssetInjector $assetInjector)
    {
        $this->assetInjector = $assetInjector;
    }

    /**
     * @inheritDoc
     */
    public function collect(Request $request, Response $response, ?Throwable $exception = NULL)
    {
        $this->data = [
            'packageCount'=>$this->assetInjector->getPackages()->count(),
            'beforeData'=>$this->assetInjector->getBeforeRenderData(),
            'afterData'=>$this->assetInjector->getAfterRenderData(),
            'afterAssetCount'=>$this->assetInjector->getAfterAssetCount(),
            'beforeAssetCount' =>$this->assetInjector->getBeforeAssetCount(),
            'totalAssetCount'=>$this->assetInjector->getBeforeAssetCount()+$this->assetInjector->getAfterAssetCount(),
            'injectorVersion'=>$this->assetInjector->getVersion(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'asset_injector.assetinjector_collector';
    }

    public function reset()
    {
        $this->data = [];
    }

    public function getInjectorVersion()
    {
        return $this->data['injectorVersion'];
    }

    public function getPackageCount()
    {
        return $this->data['packageCount'];
    }

    public function getTotalAssetCount()
    {
        return $this->data['totalAssetCount'];
    }

    public function getBeforeAssetCount()
    {
        return $this->data['beforeAssetCount'];
    }

    public function getAfterAssetCount()
    {
        return $this->data['afterAssetCount'];
    }

    public function getBeforeData()
    {
        return $this->data['beforeData'];
    }

    public function getAfterData()
    {
        return $this->data['afterData'];
    }
}