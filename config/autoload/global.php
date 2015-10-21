<?php
/**
 * Global Configuration Override
 *
 * You can use this file for overriding configuration values from modules, etc.
 * You would place values in here that are agnostic to the environment and not
 * sensitive to security.
 *
 * @NOTE: In practice, this file will typically be INCLUDED in your source
 * control, so do not include passwords or other sensitive information in this
 * file.
 */
 
return array(
     'db' => array(
                                            // 'platform' => 'SqlServer',
                                        'platform' => 'SqlServer',
                                        'driver'   => 'Pdo',
                                        'dsn'      => 'dblib:host=CRMSQL:1433;dbname=CRM;',
                                        'username' => 'usrcrm',
                                        'password' => 'Passusrcrm.,',
                                        'pdotype'  => 'dblib',
                                       // 'pdotype'  => 'dblib',  
      /*  'adapters' => array (
        'crm' => array(                       
                                   
              ),
        ),*/
     ),
     
     'service_manager' => array(
        'factories' => array(
            'Zend\Db\Adapter'=>'Zend\Db\Adapter\AdapterServiceFactory',
        ),
        
        'abstract_factories' => array(
            'Zend\Db\Adapter\AdapterAbstractServiceFactory',
        ),
        )
);