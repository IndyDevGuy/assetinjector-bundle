<?php
namespace IndyDevGuy\AssetInjectorBundle\Service;

use IndyDevGuy\AssetInjectorBundle\Modal\Package\AssetInjectorPackageInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Twig\Environment;

class AssetInjector
{
    private $dispatcher;
    private $packages;
    private $beforeRenderData;
    private $afterRenderData;
    private $rendered;
    private $beforeAssetCount;
    private $afterAssetCount;
    private $version;

    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
        $this->packages = new ArrayCollection();
        $this->rendered = false;
        $this->beforeRenderData = array();
        $this->afterRenderData = array();
        $this->rendered = false;
        $this->beforeAssetCount = 0;
        $this->afterAssetCount = 0;
        $this->version = 'v1.0';
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function getBeforeRenderData()
    {
        return $this->beforeRenderData;
    }

    public function getAfterRenderData()
    {
        return $this->afterRenderData;
    }

    public function addPackage(AssetInjectorPackageInterface $package)
    {
        if(!$this->packages->contains($package)) {
            $this->packages->add($package);
            //var_dump('added package');
        }
    }

    public function removePackage(AssetInjectorPackageInterface $package)
    {
        if($this->packages->contains($package)) {
            $this->packages->removeElement($package);
        }
    }

    public function getPackages():ArrayCollection
    {
        $this->orderPackages();
        return $this->packages;
    }

    public function getBeforeAssetCount()
    {
        return $this->beforeAssetCount;
    }

    public function getAfterAssetCount()
    {
        return $this->afterAssetCount;
    }

    private function orderPackages()
    {
        $iterator = $this->packages->getIterator();
        $iterator->uasort(function ($first, $second) {
            if ($first === $second) {
                return 0;
            }
            return $first->getPriority() > $second->getPriority() ? -1 : 1;
        });
        $this->packages = new ArrayCollection(iterator_to_array($iterator));
    }

    private function setRenderData()
    {
        if($this->rendered == false) {
            foreach ($this->packages as $package) {
                if ($package instanceof AssetInjectorPackageInterface) {
                    //var_dump('Package '.$package->getName().' found, checking if it has been rendered');
                    if (!$package->getRendered()) {
                        //var_dump('Package '.$package->getName().' has not been rendered, rendering package...');
                        $packageRendered = $package->render();
                        if ($packageRendered == true) {
                            //var_dump('Package '.$package->getName().' has been rendered, adding to render data arrays');
                            $this->beforeRenderData[] = $package->getBeforeRenderData();
                            $this->afterRenderData[] = $package->getAfterRenderData();
                        }
                    }
                }
            }
        }
        $beforeData = array_filter($this->beforeRenderData);
        $this->beforeRenderData = array_values($beforeData);
        $afterData = array_filter($this->afterRenderData);
        $this->afterRenderData = array_values($afterData);
    }

    public function render()
    {
        $this->orderPackages();
        $this->setRenderData();

        //go thru our render data and add it the the page content
        $this->dispatcher->addListener('kernel.response', function($event) {
            //gets the current response to be modified
            $response = $event->getResponse();
            //gets the current content inside the response
            $content = $response->getContent();


            $fP = 0;
            foreach ($this->beforeRenderData as $data) {
                if (is_array($data)) {
                    $sP = 0;
                    foreach($data as $beforeData) {
                        if ($beforeData['displayed'] == false) {

                            if($beforeData['type'] == 'js')
                            {
                                $content = $this->writeDataToContent($content,'<!-- BLOCK JAVASCRIPTS -->',26,$beforeData['data']);
                                if(isset($this->beforeRenderData[$fP][$sP])) {
                                    $this->beforeRenderData[$fP][$sP]['displayed'] = true;
                                    ++$this->beforeAssetCount;
                                }
                            }
                            elseif($beforeData['type'] == 'css')
                            {
                                $content = $this->writeDataToContent($content,'<!-- BLOCK STYLESHEETS -->',26,$beforeData['data']);
                                if(isset($this->beforeRenderData[$fP][$sP])){
                                    $this->beforeRenderData[$fP][$sP]['displayed'] = true;
                                    ++$this->beforeAssetCount;
                                }
                            }
                            elseif($beforeData['type'] == 'twig')
                            {
                                $writeLocation = '<!-- BLOCK JAVASCRIPTS -->';
                                if (strpos($beforeData['data'], '.css') !== false) {
                                    $writeLocation = '<!-- BLOCK STYLESHEETS -->';
                                }
                                $content = $this->writeDataToContent($content,$writeLocation,26,$beforeData['data']);
                                if(isset($this->beforeRenderData[$fP][$sP])){
                                    $this->beforeRenderData[$fP][$sP]['displayed'] = true;
                                    ++$this->beforeAssetCount;
                                }
                            }
                        }
                        ++$sP;
                    }
                }
                ++$fP;
            }
            $fP = 0;
            foreach ($this->afterRenderData as $data) {
                if (is_array($data)) {
                    $sP = 0;
                    foreach($data as $afterData) {
                        if ($afterData['displayed'] == false) {
                            //var_dump('display is false');
                            //var_dump($afterData);
                            if($afterData['type'] == 'js')
                            {
                                $content = $this->writeDataToContent($content,'<!-- ENDBLOCK JAVASCRIPTS -->',0,$afterData['data']);
                                if(isset($this->afterRenderData[$fP][$sP])){
                                    $this->afterRenderData[$fP][$sP]['displayed'] = true;
                                    ++$this->afterAssetCount;
                                }
                            }
                            elseif($afterData['type'] == 'css')
                            {
                                $content = $this->writeDataToContent($content,'<!-- ENDBLOCK STYLESHEETS -->',0,$afterData['data']);
                                if(isset($this->afterRenderData[$fP][$sP])){
                                    $this->afterRenderData[$fP][$sP]['displayed'] = true;
                                    ++$this->afterAssetCount;
                                }
                            }
                            elseif($afterData['type'] == 'twig')
                            {
                                //var_dump('type is twig');
                                $writeLocation = '<!-- ENDBLOCK JAVASCRIPTS -->';
                                if (strpos($afterData['data'], '.css') !== false) {
                                    $writeLocation = '<!-- ENDBLOCK STYLESHEETS -->';
                                }
                                $content = $this->writeDataToContent($content,$writeLocation,0,$afterData['data']);
                                if(isset($this->afterRenderData[$fP][$sP])){
                                    $this->afterRenderData[$fP][$sP]['displayed'] = true;
                                    ++$this->afterAssetCount;
                                }
                            }
                            //var_dump($this->afterRenderData);
                            //exit();
                        }
                        ++$sP;
                    }
                }
                ++$fP;
            }
            //var_dump($this->afterRenderData);
            //exit();
            $this->rendered = true;

            //set the content to the response
            $response->setContent($content);
            //set the response to the event
            $event->setResponse($response);

        });

    }

    private function writeDataToContent(string $content,string $writeLocation,int $offset, $data):string
    {
        $pos = strripos($content, $writeLocation) + $offset;
        $content = substr($content, 0, $pos) . $data . substr($content, $pos);
        //var_dump('data written to content');
        return $content;
    }



}