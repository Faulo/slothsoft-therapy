<?php
declare(strict_types = 1);
namespace Slothsoft\Therapy\Assets;

use Slothsoft\Farah\FarahUrl\FarahUrlArguments;
use Slothsoft\Farah\Module\Module;
use Slothsoft\Farah\Module\Asset\AssetInterface;
use Slothsoft\Farah\Module\Asset\ExecutableBuilderStrategy\ExecutableBuilderStrategyInterface;
use Slothsoft\Farah\Module\Executable\ExecutableStrategies;
use Slothsoft\Farah\Module\Executable\ResultBuilderStrategy\DOMWriterResultBuilder;
use Slothsoft\Therapy\Clinic;
use Slothsoft\Farah\FarahUrl\FarahUrl;

class FormBuilder implements ExecutableBuilderStrategyInterface
{

    public function buildExecutableStrategies(AssetInterface $context, FarahUrlArguments $args): ExecutableStrategies
    {
        $clinic = new Clinic();
        
        $clinic->loadPatients(...$this->getPatientAssets($context->createUrl()
            ->withPath('/static/wochenziele')));
        
        if ($data = $args->get('clinic')) {
            if (is_array($data)) {
                $clinic->setData($data);
                $clinic->save();
            }
        }
        
        $resultBuilder = new DOMWriterResultBuilder($clinic);
        return new ExecutableStrategies($resultBuilder);
    }

    private function getPatientAssets(FarahUrl $goalsUrl): iterable
    {
        $goalsAsset = Module::resolveToAsset($goalsUrl);
        
        return $goalsAsset->getAssetChildren();
    }
}

