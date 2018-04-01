<?php
declare(strict_types = 1);
namespace Slothsoft\Therapy\Assets;

use Slothsoft\Farah\Module\FarahUrl\FarahUrl;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlPath;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetImplementation;
use Slothsoft\Farah\Module\Results\ResultInterface;
use Slothsoft\Therapy\Clinic;
use Slothsoft\Farah\Module\Results\ResultCatalog;

class FormController extends AssetImplementation
{

    protected function loadResult(FarahUrl $url): ResultInterface
    {
        $args = $url->getArguments();
        
        $clinic = new Clinic();
        
        $clinic->loadPatients($this->getPatientAssets());
        
        if ($data = $args->get('clinic')) {
            if (is_array($data)) {
                $clinic->setData($data);
                $clinic->save();
            }
        }
        
        return ResultCatalog::createDOMWriterResult($url, $clinic);
    }

    private function getPatientAssets(): array
    {
        $goalsPath = FarahUrlPath::createFromString('static/wochenziele');
        $goalsUrl = $this->getOwnerModule()->createUrl($goalsPath, FarahUrlArguments::createEmpty());
        $goalsAsset = FarahUrlResolver::resolveToAsset($goalsUrl);
        
        return $goalsAsset->getAssetChildren();
    }
}

