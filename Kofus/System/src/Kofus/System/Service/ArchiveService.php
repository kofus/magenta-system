<?php
namespace Kofus\System\Service;
use Kofus\System\Service\AbstractService;
use Kofus\Archive\Sqlite\Table;


class ArchiveService extends AbstractService
{
    public function uriStack()
    {
        return new UriStackService();
    }
    
   
    
}