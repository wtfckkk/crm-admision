<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class UsuarioPerfilTable extends TableGateway
{
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('USUARIO_PERFIL', $adapter, $databaseSchema,$selectResultPrototype);
    }

    
    public function getDatos2()
    {
        
        $datos = $this->select();
        $recorre = $datos->toArray();
                      
        return $recorre;
    }
    
    public function fetchAll()
{
    $resultSet = $this->select(function(Select $select){
        $select->quantifier('TOP 15 ')
            ->order('id ASC');
    });
    return $resultSet;
}
    
    public function getPerfil($user)
    {
       $datos = $this->select(array('USERNAME'=>$user));
       $recorre = $datos->toArray();
                      
        return $recorre;
    }
    

    
   
    
}