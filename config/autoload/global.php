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
        'adapters' => array (
        'crm' => array(
                          /*  'username'=>'crm',
                            'password'=>'crm',
                            'driver'=>'Sqlsrv',
                            'dsn'=>'odbc:driver=sqlserver11;dbname=crm;server=172.25.6.85;Trusted_Connection=No',                            
                            'driver_options'=>array(
                                PDO::MYSQL_ATTR_INIT_COMMAND=>'SET NAMES \'utf8\''
                            ),   */                         
                                       // 'platform' => 'SqlServer',
                                        'driver'   => 'Pdo_odbc',
                                        'dsn'      => 'odbc:driver=nativesql;Server=172.25.6.85',
                                        'database' => 'crm',
                                        'username' => 'crm',
                                        'password' => 'crm',
                                       // 'pdotype'  => 'dblib',                                     
              ),
            /*'crm' => array(      
               // 'platform' => 'SqlServer',
                'driver' => 'Sqlsrv', 
                'database' => 'crm',               
               // 'dsn' => 'odbc:driver=sqlserver11;database=crm;SERVER=172.25.6.85;Port=1433',
                'dsn' => 'pdo:Database=crm;Server=172.25.6.85',
                'user'=> 'crm',
                'password'=> 'crm',
                //DRIVER={sqlserver11};HOSTNAME=172.25.6.85;DATABASE=crm;',                
            //    'charset' => 'UTF-8',
            //    'pdotype' => 'odbc',    
            ),*/
            
        ),
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