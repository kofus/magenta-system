<?php

namespace Kofus\System\View\Helper;
use Zend\View\Helper\AbstractHelper;



class UrlBackHelper extends AbstractHelper 
{
    public function __invoke()
    {
    	return $this->getView()->url('kofus_system', array('controller' => 'uri-stack', 'action' => 'go-back', 'id' => md5($_SERVER['REQUEST_URI'])));
    }
    

}


