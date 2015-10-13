<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class TipoFeedbackTable extends TableGateway
{
   
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('TIPO_FEEDBACK', $adapter, $databaseSchema,$selectResultPrototype);
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
    
    public function getCombo()
    {   
        $datos = $this->select();
        $recorre = $datos->toArray();
        $resultado["0"]="Seleccione tipo de feedback";
        for($i=0;$i<count($recorre);$i++)
        {
          $resultado[$recorre[$i]['ID_TIPO']] = $recorre[$i]['DESC_TIPO']; 
        }
        return $recorre;
    }
    
    public function getDatos(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA='dbo' AND TABLE_NAME='CARRERAS'";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    

    
   
    
}