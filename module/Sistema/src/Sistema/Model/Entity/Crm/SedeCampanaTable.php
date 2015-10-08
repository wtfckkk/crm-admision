<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class SedeCampanaTable extends TableGateway
{
    private $ID_CAMPANA;
    private $COD_SEDE;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('SEDE_CAMPANA', $adapter, $databaseSchema,$selectResultPrototype);
    }

    public function nuevaSedeCampana($USERNAME,$COD_SEDE)
    {             
             $array=array
             (
                'ID_CAMPANA'=>$ID_CAMPANA,
                'COD_SEDE'=>$COD_SEDE,

             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
    } 
    public function getIDCampana($codesede)
    {
        
        $datos = $this->select(array('COD_SEDE'=>$codesede));
        $recorre = $datos->toArray();
         for($i=0;$i<count($recorre);$i++)
        {
          $result[$i] = $recorre[$i]['ID_CAMPANA']; 
        }             
        return $result;
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