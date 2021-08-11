<?php
namespace Kofus\System;

include_once __DIR__ . '/../../framework.config.php';

return array(
    'controllers' => array(
        'invokables' => array(
            'Kofus\System\Controller\Database' => 'Kofus\System\Controller\DatabaseController',
            'Kofus\System\Controller\Doctrine' => 'Kofus\System\Controller\DoctrineController',
            'Kofus\System\Controller\Node' => 'Kofus\System\Controller\NodeController',
            'Kofus\System\Controller\Relation' => 'Kofus\System\Controller\RelationController',
            'Kofus\System\Controller\Content' => 'Kofus\System\Controller\ContentController',
            'Kofus\System\Controller\Page' => 'Kofus\System\Controller\PageController',
            'Kofus\System\Controller\Error' => 'Kofus\System\Controller\ErrorController',
            'Kofus\System\Controller\Search' => 'Kofus\System\Controller\SearchController',
            'Kofus\System\Controller\Redirect' => 'Kofus\System\Controller\RedirectController',
            'Kofus\System\Controller\Console' => 'Kofus\System\Controller\ConsoleController',
            'Kofus\System\Controller\Test' => 'Kofus\System\Controller\TestController',
            'Kofus\System\Controller\Tag' => 'Kofus\System\Controller\TagController',
            'Kofus\System\Controller\TagVocabulary' => 'Kofus\System\Controller\TagVocabularyController',
            'Kofus\System\Controller\Autologout' => 'Kofus\System\Controller\AutologoutController',
            'Kofus\System\Controller\Translations' => 'Kofus\System\Controller\TranslationsController',
            'Kofus\System\Controller\UriStack' => 'Kofus\System\Controller\UriStackController'
        )
    ),
    
    'user' => array(
        'acl' => array(
            'resources' => array(
                'System'
            )
        ),
        'controller_mappings' => array(
            'Kofus\System\Controller\Database' => 'System',
            'Kofus\System\Controller\Doctrine' => 'System',
            'Kofus\System\Controller\Search' => 'System',
            'Kofus\System\Controller\Batch' => 'System',
            'Kofus\System\Controller\Cron' => 'Frontend',
            'Kofus\System\Controller\CronScheduler' => 'System',
            'Kofus\System\Controller\Relation' => 'System',
            'Kofus\System\Controller\Redirect' => 'Frontend',
            'Kofus\System\Controller\Console' => 'Console',
            'Kofus\System\Controller\Test' => 'System',
            'Kofus\System\Controller\Translations' => 'System',
            'Kofus\System\Controller\UriStack' => 'Frontend'
        )
    ),
    
    'controller_plugins' => array(
        'invokables' => array(
            'em' => 'Kofus\System\Controller\Plugin\EmPlugin',
            'nodes' => 'Kofus\System\Controller\Plugin\NodesPlugin',
            'links' => 'Kofus\System\Controller\Plugin\LinksPlugin',
            'config' => 'Kofus\System\Controller\Plugin\ConfigPlugin',
            'translations' => 'Kofus\System\Controller\Plugin\TranslationsPlugin',
            'formBuilder' => 'Kofus\System\Controller\Plugin\FormPlugin',
            'fb' => 'Kofus\System\Controller\Plugin\FormBuilderPlugin',
            'translator' => 'Kofus\System\Controller\Plugin\TranslatorPlugin',
            'locale' => 'Kofus\System\Controller\Plugin\LocalePlugin',
            'paginator' => 'Kofus\System\Controller\Plugin\PaginatorPlugin',
            'lucene' => 'Kofus\System\Controller\Plugin\LucenePlugin',
            'viewHelper' => 'Kofus\System\Controller\Plugin\ViewHelperPlugin',
            'settings' => 'Kofus\System\Controller\Plugin\SettingsPlugin',
            'archive' => 'Kofus\System\Controller\Plugin\ArchivePlugin',
            
        )
    ),
    
    'public_paths' => array(
        __DIR__ . '/../public'
    ),
    
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ . '_driver' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . str_replace('\\', '/', __NAMESPACE__) . '/Entity'
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ . '_driver'
                )
            )
        )
    ),
    
    
    
    'router' => array(
        'routes' => array(
            
            'error' => array(
                'type' => 'Kofus\System\Mvc\ErrorRoute',
                'may_terminate' => true,
                'options' => array(
                    'defaults' => array(
                        '__NAMESPACE__' => 'Kofus\System\Controller',
                        'controller' => 'error',
                        'action' => 'index'
                    )
                )
            ),
            'kofus_system' => array(
                'type' => 'Segment',
                'options' => array(
                    'route' => '/:language/' . KOFUS_ROUTE_SEGMENT . '/system/:controller[/:action[/:id[/:id2]]]',
                    'constraints' => array(
                        'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'language' => '[a-z][a-z]'
                    ),
                    'defaults' => array(
                        'language' => 'de',
                        'action' => 'index',
                        '__NAMESPACE__' => 'Kofus\System\Controller'
                    )
                ),
                'may_terminate' => true
            ),

        )
    ),
    'view_manager' => array(
        'doctype' => 'HTML5',
        'not_found_template' => 'kofus/error/404',
        'exception_template' => 'kofus/error/exception',
        'template_map' => array(
            'layout/admin' => __DIR__ . '/../view/layout/backend.phtml'
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view'
        ),
        'controller_map' => array(
            'Kofus\System' => true
        ),
        'strategies' => array(
            'ViewJsonStrategy'
        ),
        'module_layouts' => array(
            'Kofus\System\Controller\Database' => 'kofus/layout/admin',
            'Kofus\System\Controller\Doctrine' => 'kofus/layout/admin',
            'Kofus\System\Controller\Node' => 'kofus/layout/admin',
            'Kofus\System\Controller\Page' => 'kofus/layout/admin',
            'Kofus\System\Controller\Relation' => 'kofus/layout/admin',
            'Kofus\System\Controller\Search' => 'kofus/layout/admin',
            'Kofus\System\Controller\TagVocabulary' => 'kofus/layout/admin',
            'Kofus\System\Controller\Tag' => 'kofus/layout/admin',
            'Kofus\System\Controller\Translations' => 'kofus/layout/admin',
        )
    ),
    
    'view_helpers' => array(
        'invokables' => array(
            'flashMessages' => 'Kofus\System\View\Helper\FlashMessagesHelper',
            'bodyTag' => 'Kofus\System\View\Helper\BodyTagHelper',
            'assets' => 'Kofus\System\View\Helper\AssetsHelper',
            'optimizer' => 'Kofus\System\View\Helper\OptimizerHelper',
            'config' => 'Kofus\System\View\Helper\ConfigHelper',
            'kofusNavigation' => 'Kofus\System\View\Helper\NavigationHelper',
            'locale' => 'Kofus\System\View\Helper\LocaleHelper',
            'translateNode' => 'Kofus\System\View\Helper\TranslateNodeHelper',
            'translateLink' => 'Kofus\System\View\Helper\TranslateLinkHelper',
            'navTree' => 'Kofus\System\View\Helper\Navigation\TreeHelper',
            'nodes' => 'Kofus\System\View\Helper\NodesHelper',
            'formFieldset' => 'Kofus\System\View\Helper\Form\FieldsetHelper',
            'spamSpan' => 'Kofus\System\View\Helper\SpamSpanHelper',
            'paginationColumnSort' => 'Kofus\System\View\Helper\PaginationColumnSortHelper',
            'session' => 'Kofus\System\View\Helper\SessionHelper',
            'nodeNavigation' => 'Kofus\System\View\Helper\NodeNavigationHelper',
            'shortenString' => 'Kofus\System\View\Helper\ShortenStringHelper',
            'implodeValidPieces' => 'Kofus\System\View\Helper\ImplodeValidPiecesHelper',
            'settings' => 'Kofus\System\View\Helper\SettingsHelper',
            'duration' => 'Kofus\System\View\Helper\DurationHelper',
            'filter' => 'Kofus\System\View\Helper\FilterHelper',
            'searchResult' => 'Kofus\System\View\Helper\SearchResultHelper',
            'formHtml' => 'Kofus\System\View\Helper\Form\HtmlHelper',
            'urlBack' => 'Kofus\System\View\Helper\UrlBackHelper'
            
        ),
    ),
    
    'service_manager' => array(
        'factories' => array(
            'logger' => 'Zend\Log\LoggerServiceFactory',
            'MvcTranslator' => 'Kofus\System\Mvc\Service\TranslatorServiceFactory',
            
            'Cache' => function ($sm) {
                if (! is_dir('data/cache'))
                    mkdir('data/cache', 0777);
                return new \Zend\Cache\Storage\Adapter\Filesystem(array(
                    'cache_dir' => 'data/cache',
                    'ttl' => 3600, // 1h
                    'key_pattern' => '/^[a-z0-9\.]*$/Di'
                ));
            },
            'SessionCache' => function ($sm) {
                $session = $sm->get('Zend\Session\SessionManager');
                return new \Zend\Cache\Storage\Adapter\Filesystem(array(
                    'cache_dir' => 'data/cache',
                    'namespace' => $session->getId(),
                    'ttl' => 3600, // 1h
                    'key_pattern' => '/^[a-z0-9\.]*$/Di'
                ));
            }
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator'
        ),
        'invokables' => array(
            // Services
            'KofusDatabase' => 'Kofus\System\Service\DatabaseService',
            'KofusConfig' => 'Kofus\System\Service\ConfigService',
            'KofusConfigService' => 'Kofus\System\Service\ConfigService',
            'KofusLocale' => 'Kofus\System\Service\LocaleService',
            'KofusLocaleService' => 'Kofus\System\Service\LocaleService',
            'KofusNodeService' => 'Kofus\System\Service\NodeService',
            'KofusFormService' => 'Kofus\System\Service\FormService',
            'KofusNavigationService' => 'Kofus\System\Service\NavigationService',
            'KofusTranslationService' => 'Kofus\System\Service\TranslationService',
            'KofusLinkService' => 'Kofus\System\Service\LinkService',
            'KofusLuceneService' => 'Kofus\System\Service\LuceneService',
            'KofusSettingsService' => 'Kofus\System\Service\SettingsService',
            'KofusSettings' => 'Kofus\System\Service\SettingsService',
            'KofusFormBuilderService' => 'Kofus\System\Service\FormBuilderService',
            'KofusFormWizardService' => 'Kofus\System\Service\FormWizardService',
            'KofusArchiveService' => 'Kofus\System\Service\ArchiveService',
            
            // Crons
            'KofusBatchService' => 'Kofus\System\Service\BatchService',
            'KofusDbBackupCron' => 'Kofus\System\Cron\DbBackupCron',
            'KofusTestMailCron' => 'Kofus\System\Cron\TestMailCron',
            'KofusLuceneUpdateCron' => 'Kofus\System\Cron\LuceneUpdateCron',
            'KofusLuceneCron' => 'Kofus\System\Cron\LuceneCron',
            
            // Listeners
            'KofusPublicFilesListener' => 'Kofus\System\Listener\PublicFilesListener',
            'KofusNodeListener' => 'Kofus\System\Listener\NodeListener',
            'KofusLuceneListener' => 'Kofus\System\Listener\LuceneListener',
            'KofusI18nListener' => 'Kofus\System\Listener\I18nListener',
            'KofusLayoutListener' => 'Kofus\System\Listener\LayoutListener'
        
        )
    )
);
