<?php
declare(strict_types = 1);
namespace Slothsoft\Therapy\Assets;

use Slothsoft\Farah\Module\Executables\ExecutableCreator;
use Slothsoft\Farah\Module\Executables\ExecutableInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetBase;
use Slothsoft\Therapy\Clinic;

class FormController extends AssetBase
{

    protected function loadExecutable(FarahUrlArguments $args): ExecutableInterface
    {
        $clinic = new Clinic();
        
        $clinic->loadPatients(...$this->getPatientAssets());
        
        if ($data = $args->get('clinic')) {
            if (is_array($data)) {
                $clinic->setData($data);
                $clinic->save();
            }
        }
        
        $creator = new ExecutableCreator($this, $args);
        return $creator->createDOMWriterExecutable($clinic);
    }

    private function getPatientAssets(): array
    {
        $goalsUrl = $this->getOwnerModule()->createUrl('/static/wochenziele');
        $goalsAsset = FarahUrlResolver::resolveToAsset($goalsUrl);
        
        return $goalsAsset->getAssetChildren();
    }
}

