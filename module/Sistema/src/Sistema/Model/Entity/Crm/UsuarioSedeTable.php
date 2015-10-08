<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class UsuarioSedeTable extends TableGateway
{
    private $USERNAME;
    private $COD_SEDE;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('USUARIO_SEDE', $adapter, $databaseSchema,$selectResultPrototype);
    }

    public function nuevoUsuSede($USERNAME,$COD_SEDE)
    {             
             $array=array
             (
                'USERNAME'=>$USERNAME,
                'COD_SEDE'=>$COD_SEDE,

             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
    } 
    public function getSede($user)
    {
        
        $datos = $this->select(array('USERNAME'=>$user));
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
    
    public function getDatos(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='dbo' AND TABLE_NAME='CARRERAS'";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    

    
   
    
}