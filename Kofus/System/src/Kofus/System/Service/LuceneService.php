<?php
namespace Kofus\System\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Kofus\System\Node\NodeInterface;
use Kofus\System\Service\AbstractService;


class LuceneService extends AbstractService
{
    protected $indexPath = 'data/lucene';
    protected $indexes = array();
   
    /**
     * Get existing index.
     * Build a new index if it does not exist yet.
     * @return \ZendSearch\Lucene\SearchIndexInterface
     */
    public function getIndex($locale='default')
    {
    	if (! $locale) $locale = \Locale::getDefault();
        if (! isset($this->indexes[$locale])) {
            
            \ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
                new \ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num\CaseInsensitive()
            );
            \ZendSearch\Lucene\Search\QueryParser::setDefaultEncoding('utf-8');
            
            $path = $this->indexPath . '/' . $locale;
        	if (is_dir($path)) {
        		$this->indexes[$locale] = new \Kofus\System\Search\Index($path, false);
        	} else {
        		$this->indexes[$locale] = new \Kofus\System\Search\Index($path, true);
        	}
        }
        return $this->indexes[$locale];
    }
    
    public function updateModifiedNodes($nodeType, $locale='default')
    {
   		$index = $this->getIndex($locale);
    	$settings = $this->getServiceLocator()->get('KofusSettings');
    	$key = 'lucene.timestamp.modified.' . $nodeType;
    	$timestampModified = \DateTime::createFromFormat('Y-m-d H:i:s', $settings->getSystemValue($key, '1970-01-01 00:00:00'));
    	
    	print 'timestamp last modified: ' . $timestampModified->format('Y-m-d H:i:s') . "\n";
    	
		$classnames = $this->config()->get('nodes.available.'.$nodeType.'.search_documents', array());
		foreach ($classnames as $classname) {
			$nodes = $this->nodes()->createQueryBuilder($nodeType)
				->where('n.timestampModified > :timestampModified')
				->setParameter('timestampModified', $timestampModified)
				->getQuery()->getResult();

			foreach ($nodes as $node) {
				print $node . "\n";
				
				// Delete existing entries
				$hits = $index->find("node_id: '" . $node->getNodeId() . "'");
				foreach ($hits as $hit)
					$index->delete($hit);
				
				$document = new $classname();
				if ($document instanceof ServiceLocatorAwareInterface) {
					$document->setServiceLocator($this->getServiceLocator());
					$document->populateNode($node);
					$index->addDocument($document);
				}
			}
 			$index->commit();
    		$index->optimize();
		}
		$now = new \DateTime();
		$settings->setSystemValue($key, $now->format('Y-m-d H:i:s'));
    }
    
    public function updateNode(NodeInterface $node, $locale='default')
    {
        $index = $this->getIndex($locale);

		
    	// Delete existing node
		$hits = $index->find("node_id: '" . $node->getNodeId() . "'");
		foreach ($hits as $hit)
			$index->delete($hit);
        
        $classnames = $this->config()->get('nodes.available.'.$node->getNodeType().'.search_documents', array());
        foreach ($classnames as $classname) {
    		$document = new $classname();
    		if ($document instanceof ServiceLocatorAwareInterface)
    			$document->setServiceLocator($this->getServiceLocator());
    		$document->populateNode($node, $locale);
    		$index->addDocument($document);
        }
        
    	$index->commit();
    	$index->optimize();
    }
    
    public function deleteNode(NodeInterface $node, $locale='default')
    {
    	$index = $this->getIndex($locale);
    	$hits = $index->find("node_id: '" . $node->getNodeId() . "'");
    	foreach ($hits as $hit)
    		$index->delete($hit);
    	$index->commit();
    	$index->optimize();
    }
    
    public function deleteNodeType($nodeType)
    {
    	$index = $this->getIndex();
    	$hits = $index->find("node_type: '" . $nodeType . "'");
    	foreach ($hits as $hit)
    		$index->delete($hit);
    	$index->commit();
    	$index->optimize();    	
    }
    
    public function reindex($nodeTypes=null, $locales=array('default'))
    {
        $debug = '';
        ini_set('max_execution_time', 0);
        
        if (is_string($nodeTypes))
            $nodeTypes = array($nodeTypes);
        
        if (null === $nodeTypes)
            $nodeTypes = $this->config()->get('nodes.enabled');
        
        if (null === $locales)
            $locales = $this->config()->get('locales.enabled');
            
        foreach ($locales as $locale) {
        	$index = $this->getIndex($locale);
        
	        foreach ($nodeTypes as $nodeType) {
	            
	            if (! $this->config()->get('nodes.available.' . $nodeType . '.search_documents')) continue;
	            
	            print 'old entries: ' . count($index->find('node_type: ' . $nodeType)) . PHP_EOL;
	        
	            // Delete existing entries
	            $hits = $index->find("node_type: '" . $nodeType . "'");
	            foreach ($hits as $hit)
	            	$index->delete($hit);
	            $index->commit();
	            $index->optimize();
	            
	            // Add new entries
	            switch ($nodeType) {
	            	case 'LANGUAGE':
	            		$languages = $this->config()->get('nodes.available.LANGUAGE.values');
	            		foreach ($languages as $id) {
	            			$label = \Locale::getDisplayLanguage($id, $locale);
	            			$document = new \Kofus\System\Search\Document\LanguageDocument();
	            			$document->populate($id, $label);
	            			$index->addDocument($document);
	            		}
	            		break;
	            		
	            	case 'COUNTRY':
	            		$countries = $this->config()->get('nodes.available.COUNTRY.values');
	            		foreach ($countries as $id) {
	            			$label = \Locale::getDisplayRegion('-' . $id, $locale);
	            			$document = new \Kofus\System\Search\Document\CountryDocument();
	            			$document->populate($id, $label);
	            			$index->addDocument($document);
	            		}
	            		break;
	            		
	            	default:
	            		$classnames = $this->config()->get('nodes.available.'.$nodeType.'.search_documents', array());
	            		foreach ($classnames as $classname) {
	            		    print $classname . PHP_EOL;
	            			$nodes = $this->nodes()->getRepository($nodeType)->findAll();
	            			foreach ($nodes as $node) {
	            				$document = new $classname();
	            				if ($document instanceof ServiceLocatorAwareInterface)
	            					$document->setServiceLocator($this->getServiceLocator());
	            				$document->populateNode($node, $locale);
	            				$index->addDocument($document);
	            			}
	            		}
	            }
	            
	            $index->commit();
	            print 'new entries: ' . count($index->find('node_type: ' . $nodeType)) . PHP_EOL;
	            print 'total entries: ' . $index->numDocs() . PHP_EOL;
	            print PHP_EOL;
	        }
	        $index->optimize();
        }
        return $debug;
    }
}