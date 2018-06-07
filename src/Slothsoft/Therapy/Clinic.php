<?php
declare(strict_types = 1);
namespace Slothsoft\Therapy;

use Slothsoft\Core\DOMHelper;
use Slothsoft\Core\IO\Writable\DOMWriterDocumentFromElementTrait;
use Slothsoft\Core\IO\Writable\DOMWriterInterface;
use Slothsoft\Farah\Module\FarahUrl\FarahUrlResolver;
use Slothsoft\Farah\Module\Node\Asset\AssetInterface;
use DOMDocument;
use DOMElement;

class Clinic implements DOMWriterInterface
{
    use DOMWriterDocumentFromElementTrait;

    protected $patientList;

    public function loadPatients(AssetInterface ...$assetList)
    {
        $this->patientList = [];
        foreach ($assetList as $asset) {
            $patientPath = $asset->getRealPath();
            $patientName = $asset->getName();
            $patientDocument = FarahUrlResolver::resolveToDocument($asset->createUrl());
            
            $patient = new Patient(DOMHelper::loadXPath($patientDocument), $patientDocument->documentElement);
            $patient->setName($patientName);
            $this->patientList[$patientPath] = $patient;
        }
    }

    public function getPatientByName($name)
    {
        $ret = null;
        foreach ($this->patientList as $patient) {
            if ($patient->getName() === $name) {
                $ret = $patient;
                break;
            }
        }
        return $ret;
    }

    public function setData(array $data)
    {
        foreach ($data as $patientName => $arr) {
            if ($patient = $this->getPatientByName($patientName)) {
                $patient->setData($arr);
            }
        }
    }

    public function save()
    {
        foreach ($this->patientList as $patientPath => &$patient) {
            $doc = new DOMDocument();
            $patient->clearEmpty();
            $node = $patient->asNode($doc);
            $doc->appendChild($node);
            $doc->save($patientPath);
            
            $xpath = DOMHelper::loadXPath($doc);
            $patient = new Patient($xpath, $node);
        }
        unset($patient);
    }

    public function toElement(DOMDocument $doc): DOMElement
    {
        $retNode = $doc->createElement('clinic');
        foreach ($this->patientList as $patient) {
            $retNode->appendChild($patient->asNode($doc));
        }
        return $retNode;
    }
}