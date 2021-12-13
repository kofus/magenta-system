<?php

return array();
return array(
'assets' => array(
    'enabled' => array(
    		'kofus/layout/admin' => array (
    				'html5',
    				'jquery',
    				'bootstrap',
    				'jasny-bootstrap',
    				'bootstrap-datepicker',
    		        'bootstrap-hover-dropdown',
    				'ckeditor',
    		        'font-awesome',
    				'select2',
    				'kofus/layout/admin'
    		),
    ),
    
		'available' => array(
				'jquery' => array(
						'base_uri' => '/assets/jquery',
						'files' => array(
								'js' => array(
										'jquery.min.js'
								)
						)
				),
				'jquery.marquee' => array(
						'base_uri' => '/assets/jquery',
						'dependencies' => array('jquery'),
						'files' => array(
								'js' => array(
										'jquery.marquee.js'
								)
						)
				),
				'bootstrap' => array(
						'base_uri' => '/assets/bootstrap',
						'dependencies' => array(
								'jquery'
						),
						'files' => array(
								'js' => array(
										'js/bootstrap.min.js'
								),
								'css' => array(
										'css/bootstrap.min.css',
										'css/bootstrap-theme.min.css'
								)
						)
				),
				'abn_tree' => array(
						'base_uri' => '/assets/abn_tree',
						'dependencies' => array(
								'angular-animate'
						),
						'files' => array(
								'js' => array(
										'abn_tree_directive.js'
								),
								'css' => array(
										'abn_tree.css'
								)
						)
				),
				'angular' => array(
						'base_uri' => '/assets/angular',
						'files' => array(
								'js' => array(
										'angular.min.js'
								)
						)
				),
				'angular-animate' => array(
						'base_uri' => '/assets/angular',
						'dependencies' => array(
								'angular'
						),
						'files' => array(
								'js' => array(
										'angular-animate.min.js'
								)
						)
				),
				'bootstrap-datepicker' => array(
						'base_uri' => '/assets/bootstrap-datepicker',
						'dependencies' => array(
								'bootstrap'
						),
						'files' => array(
								'js' => array(
										'bootstrap-datepicker.min.js'
								),
								'css' => array(
										'bootstrap-datepicker3.min.css'
								)
						)
				),
				'bootstrap-hover-dropdown' => array(
						'base_uri' => '/assets/bootstrap-hover-dropdown',
						'dependencies' => array(
								'bootstrap'
						),
						'files' => array(
								'js' => array(
										'bootstrap-hover-dropdown.min.js'
								)
						)
				),
				'bootstrap-switch' => array(
						'base_uri' => '/assets/bootstrap-switch',
						'dependencies' => array(
								'bootstrap'
						),
						'files' => array(
								'js' => array(
										'bootstrap-switch.min.js'
								),
								'css' => array(
										'bootstrap-switch.min.css'
								)
						)
				),
				'bootstrap-treeview' => array(
						'base_uri' => '/assets/bootstrap-treeview',
						'dependencies' => array(
								'bootstrap'
						),
						'files' => array(
								'js' => array(
										'bootstrap-treeview.min.js'
								),
								'css' => array(
										'bootstrap-treeview.min.css'
								)
						)
				),
				'bootstrap-editable' => array(
						'base_uri' => '/assets/bootstrap-editable',
						'files' => array(
								'js' => array('js/bootstrap-editable.min.js'),
								'css' => array('css/bootstrap-editable.css')
						)
				),
				'ckeditor' => array(
						'base_uri' => '/assets/ckeditor',
						'files' => array(
								'js' => array(
										'ckeditor.js', 'config.js'
								),
						)
				),
				'html5' => array(
						'base_uri' => '/assets/html5',
						'files' => array(
								'js' => array(
										'html5shiv.js',
										'respond.min.js'
								)
						)
				),
				'jasny-bootstrap' => array(
						'base_uri' => '/assets/jasny-bootstrap',
						'dependencies' => array(
								'bootstrap'
						),
						'files' => array(
								'js' => array(
										'jasny-bootstrap.min.js'
								),
								'css' => array(
										'jasny-bootstrap.min.css'
								)
						)
				),
				'select2' => array(
						'base_uri' => '/assets/select2',
						'files' => array(
								'js' => array(
										'js/select2.full.min.js',
										//'js/i18n/de.js'
								),
								'css' => array(
										'css/select2.min.css',
										'css/select2-bootstrap.min.css'
								)
						)

				),
				'spamspan' => array(
						'base_uri' => '/assets/spamspan',
						'files' => array(
								'js' => array(
										'spamspan.js'
								)
						)
				),
				'lightbox' => array(
						'base_uri' => '/assets/lightbox',
						'files' => array(
								'js' => array(
										'js/lightbox.min.js'
								),
								'css' => array(
										'css/lightbox.css'
								)
						)
				),
				'font-awesome' => array(
						'base_uri' => '/assets/font-awesome',
						'files' => array(
								'css' => array(
										'css/font-awesome.min.css'
								)
						)
						 
				),

				'kofus/layout/admin' => array(
						'base_uri' => '/layout/admin',
						'files' => array(
								'sass' => array('styles/styles.scss'),
								'js' => array('scripts/main.js')
						)
						 
				)
		),
));