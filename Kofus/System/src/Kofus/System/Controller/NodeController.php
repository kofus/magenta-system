<?php
namespace Kofus\System\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Model\ViewModel;
use Kofus\System\Node\LinkedNodeInterface;
use Kofus\System\Node\TranslatableNodeInterface;
use Zend\View\Model\JsonModel;


class NodeController extends AbstractActionController
{
    public function addAction()
    {
        $nodeType = $this->params('id');
        $nodeTypeConfig = $this->nodes()->getConfig($nodeType);
        
        // Entity
        $entityClass = $nodeTypeConfig['entity'];
        $entity = new $entityClass();
        if ($entity instanceof ServiceLocatorAwareInterface)
        	$entity->setServiceLocator($this->getServiceLocator());
        
        // Form
        $form = $this->formBuilder()
            ->setEntity($entity)
            ->setContext($this->params('id2', 'add'))
            ->setLabelSize('col-sm-3')->setFieldSize('sm-9')
            ->buildForm()
            ->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));
        
        if ($this->getRequest()->isPost()) {
            $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
        	$form->setData($data);
        	//$this->uploader()->handleUpload($form, $entity);
        	
        	if ($form->isValid()) {
        		$this->em()->persist($entity);
        		$this->em()->flush();
        		$this->links()->rebuildLinks($entity);
        		
        	    $translator = $this->getServiceLocator()->get('translator');
        		$this->flashmessenger()->addSuccessMessage(sprintf($translator->translate('Added %s'), $translator->translate($nodeTypeConfig['label']) . ' ' . $entity->getNodeId()));
        		
        		$filter = new \Kofus\System\Filter\SubstitutionFilter();
        		$filter->setParams(array('node_id' => $entity->getNodeId()));
        		$nodeTypeConfig = $filter->filter($nodeTypeConfig);
        		
        		$redirectRoute = null;
        		if (isset($nodeTypeConfig['form']['add']['redirect']['route'])) {
        			$redirectRoute = $nodeTypeConfig['form']['add']['redirect']['route'];
        		} elseif (isset($nodeTypeConfig['form']['default']['redirect']['route'])) {
        			$redirectRoute = $nodeTypeConfig['form']['default']['redirect']['route'];
        		}
        		
        		if ($redirectRoute) {
        			return call_user_func_array(array($this->redirect(), 'toRoute'), $redirectRoute);	
        		} else {
        			return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
        		}
        	}
        }
        
        return new ViewModel(array(
            'form' => $form->prepare(), 
            'nodeType' => $nodeType,
            'nodeTypeConfig' => $nodeTypeConfig,
            'formTemplate' => 'kofus/system/node/form/panes.phtml'
        ));
    }
    
    public function editAction()
    {
        $locales = $this->config()->get('locales.available', array('de_DE'));
        $translator = $this->getServiceLocator()->get('translator');
        $filterUnderscore = new \Zend\Filter\Word\CamelCaseToUnderscore();
        
        
    	// Entity
    	$entity = $this->nodes()->getNode($this->params('id'));
    	if (! $entity instanceof TranslatableNodeInterface) $locales = array($this->config()->get('locales.default'));
    	$nodeTypeConfig = $this->nodes()->getConfig($entity->getNodeType());
    	 
    	// Form
    	$fb = $this->formBuilder()
        	->setEntity($entity)
        	->setContext($this->params('id2', 'edit'))
        	->setLabelSize('col-sm-3')->setFieldSize('sm-9');
    	foreach ($locales as $locale) {
    	    if ($locale != $this->config()->get('locales.default'))
    	        $fb->addTranslationFieldset($locale);   	    
    	}

    	    
    	$form = $fb->buildForm()
        	->add(new \Zend\Form\Element\Submit('submit', array('label' => 'Save')));

    	$form->bind($entity);
    	
    	if ($this->getRequest()->isPost()) {
    	    $data = array_merge_recursive($this->getRequest()->getPost()->toArray(), $this->getRequest()->getFiles()->toArray());
    		$form->setData($data);
    		//$this->uploader()->handleUpload($form, $entity);
    		if ($form->isValid()) {
    		    
    		    // Save entity
    			$this->em()->persist($entity);
    			$this->em()->flush();
    			//$this->media()->clearCache($entity);
    			
    			// Save translations
    			$translations = $this->getServiceLocator()->get('KofusTranslationService');
    			if ($entity instanceof TranslatableNodeInterface) {
    			    foreach ($form as $fieldset) {
    			    	if (! $fieldset instanceof \Zend\Form\FieldsetInterface)
    			    		continue;
    			    	$locale = substr($fieldset->getName(), 0, 5);
    			    	if (in_array($locale, $locales)) {
    			    		foreach ($entity->getTranslatableMethods() as $method => $attribute) {
    			    		    $underscoreAttribute = strtolower($filterUnderscore->filter($attribute));
    			    		    if (! $fieldset->has($attribute) && $fieldset->has($underscoreAttribute))
    			    		        $attribute = $underscoreAttribute;
    			    		    
    			    			if (! $fieldset->has($attribute)) continue;
    			    			$value = $fieldset->get($attribute)->getValue();    			    			
    			    			$translations->addNodeTranslation($entity, $method, $value, $locale);
    			    		}
    			    	}
    			    }
    			}
    			
    			$this->links()->rebuildLinks($entity);
    			
        		$this->flashmessenger()->addSuccessMessage(sprintf($translator->translate('Updated %s'), $translator->translate($nodeTypeConfig['label']) . ' ' . $entity->getNodeId()));
        		return $this->redirect()->toUrl($this->archive()->uriStack()->pop());
    		}
    	} else {
    	    
    	    // Load translations
    	    if ($entity instanceof TranslatableNodeInterface) {
    	        foreach ($form as $fieldset) {
    	            if (! $fieldset instanceof \Zend\Form\FieldsetInterface)
    	                continue;
    	            $locale = substr($fieldset->getName(), 0, 5);
    	            if (in_array($locale, $locales)) {
    	                foreach ($entity->getTranslatableMethods() as $method => $attribute) {
    	                    $underscoreAttribute = strtolower($filterUnderscore->filter($attribute));
    	                    if (! $fieldset->has($attribute) && $fieldset->has($underscoreAttribute))
    	                        $attribute = $underscoreAttribute;
    	                        
    	                    if (! $fieldset->has($attribute)) continue;
    	                	$msgId = $entity->getNodeId() . ':' . $method;
    	                	$msg = $this->em()->getRepository('Kofus\System\Entity\TranslationEntity')->findOneBy(array('msgId' => $msgId, 'locale' => $locale, 'textDomain' => 'node'));
    	                	if ($msg) {
    	                		$fieldset->get($attribute)->setValue($msg->getValue());
    	                	} else {
    	                		//$fieldset->get($attribute)->setValue($entity->$method());
    	                	}
    	                }
    	            }
    	        }
    	    }
    	}
    	
    	$formTemplate = 'kofus/system/node/form/panes.phtml';
    	if (isset($nodeTypeConfig['form']['edit']['template'])) {
    	    $formTemplate = $nodeTypeConfig['form']['edit']['template'];
    	} elseif (isset($nodeTypeConfig['form']['default']['template'])) {
    	    $formTemplate = $nodeTypeConfig['form']['default']['template'];
    	}
    	
    	
    	return new ViewModel(array(
    			'form' => $form->prepare(),
    			'nodeTypeConfig' => $nodeTypeConfig,
    			'formTemplate' => $formTemplate,
    	       'entity' => $entity,
    	       'locales' => $locales
    	));
    	 
    	 
    }
    
    
    public function deleteAction()
    {
        // Init        
        $entity = $this->nodes()->getNode($this->params('id'));
        $nodeTypeConfig = $this->nodes()->getConfig($entity->getNodeId());
        $session = new \Zend\Session\Container('NodeController_delete');
        
        if ($this->params()->fromQuery('confirm') && $this->params()->fromQuery('confirm') == $session->token) {

            // Custom factories for deletion?
            $actions = $this->config()->get('nodes.available.' . $entity->getNodeType() . '.actions.delete.factories', array());
            if ($actions) {
                foreach ($actions as $action)
                    $action($this->getServiceLocator(), $entity);
                                    
            } else {
            	// Delete cache
            	//$this->media()->clearCache($entity);
            	
                // Delete translations
                $this->translations()->deleteNodeTranslations($entity);
                
                // Delete relations
                $this->nodes()->deleteRelations($entity);
                
                // Delete cms links
                $this->links()->deleteNodeLinks($entity);
                
                // Delete node itself
            	$this->nodes()->deleteNode($entity);
            	
            }
        	
        	$this->flashmessenger()->addSuccessMessage('Node has been deleted');
        	return $this->redirect()->toRoute('admin');
        }
        $session->token = \Zend\Math\Rand::getString(8, 'abcdefghijklmnopqrstuvwxyz0123456789');
        
        return new ViewModel(array(
            'entity' => $entity,
            'nodeTypeConfig' => $nodeTypeConfig,
            'token' => $session->token
        ));
    }
    
    public function selectAction()
    {
    	$nodeTypes = explode('_', $this->params('id'));
    	
    	$q = '';
    	if (isset($_GET['q']))
    	   $q = $_GET['q'];
    	
    	$results = array();
    	
    	\ZendSearch\Lucene\Search\Query\Wildcard::setMinPrefixLength(0);
        $analyzer = new \ZendSearch\Lucene\Analysis\Analyzer\Common\Utf8Num();
    	\ZendSearch\Lucene\Analysis\Analyzer\Analyzer::setDefault(
    	    $analyzer
    	);
    	
    	$filterAlnum = new \Zend\I18n\Filter\Alnum();
    	
    	if (strlen($filterAlnum->filter($q)) > 2) {
    	    
    	    $filterAlnum = new \Zend\I18n\Filter\Alnum(true);
    	    $q = str_replace('-', ' ', $q);
    	    $words = $filterAlnum->filter($q);
    	    
    	    $query = array();
    	    foreach (explode(' ', $words) as $word) {
    	        if (strlen($word) < 3)
    	            continue;
    	        $query[] = "+'*" . $word . "*'"; 
    	    }
    	    
    	    $clause = array();
    	    foreach ($nodeTypes as $nodeType)
    	        $clause[] = "node_type:'$nodeType'";
    	    $query[] = '+(' . implode(' OR ', $clause) . ')';
    	    
    	    @ $hits = $this->lucene()->getIndex()->find(implode(' ' , $query));
    	    
    	    // Assemble result array
    	    foreach ($hits as $hit) {
    	        $label = preg_replace('/ \([a-z]+[0-9]+\)$/i', '', $hit->label);
    	        $results[] = array(
    	            'id' => $hit->node_id,
    	            'text' => $label
    	        );
    	    }
    	} elseif ($filterAlnum->filter($q) == '') {
    	    $clause = array();
    	    foreach ($nodeTypes as $nodeType)
    	        $clause[] = "node_type:'$nodeType'";
    	    $query[] = '+(' . implode(' OR ', $clause) . ')';
    	        
    	    @ $hits = $this->lucene()->getIndex()->find(implode(' ' , $query));
    	    
    	    // Assemble result array
    	    foreach ($hits as $hit) {
    	        $label = preg_replace('/ \([a-z]+[0-9]+\)$/i', '', $hit->label);
    	        $results[] = array(
    	            'id' => $hit->node_id,
    	            'text' => $label
    	        );
    	    }
    	} else {
    	    $results = array();
    	}
    	
    	
    	// Output json
    	return new JsonModel(array(
    			'count' => count($results),
    			'results' => $results,
    	));
    	 
    }
    
    

}
