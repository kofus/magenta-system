<?php

namespace Kofus\System\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class ArchivePlugin extends AbstractPlugin
{
    protected $service;
    
    public function __invoke()
    {
    	if (! $this->service)
    		$this->service = $this->getController()->getServiceLocator()->get('KofusArchiveService');
    	return $this->service;
    }
    
}