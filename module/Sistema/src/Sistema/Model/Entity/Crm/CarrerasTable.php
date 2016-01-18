<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class CarrerasTable extends TableGateway
{
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('CARRERAS', $adapter, $databaseSchema,$selectResultPrototype);
    }

    
    public function getCarrerasID($id)
    {
        
        $datos = $this->select(array('ID'=>$id));
        $recorre = $datos->toArray();
                      
        return $recorre;
    }
    
    public function getCombo(Adapter $dbAdapter,$data)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select COD_CARRERA, NOMBRE_CARRERA from CARRERAS WHERE COD_CARRERA IN($data) order by NOMBRE_CARRERA asc ";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    }
    public function getDatos(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT * FROM CARRERAS";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    

    
   
    
}