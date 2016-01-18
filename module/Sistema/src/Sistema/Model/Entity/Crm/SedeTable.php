<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class SedeTable extends TableGateway
{
   
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('SEDES', $adapter, $databaseSchema,$selectResultPrototype);
    }
    
    public function getCombo()
    {   
        $datos = $this->select();
        $recorre = $datos->toArray();
        $resultado["0"]="Seleccione tipo de campa&ntilde;a";
        for($i=0;$i<count($recorre);$i++)
        {
          $resultado[$recorre[$i]['ID_TIPO']] = $recorre[$i]['DESC_TIPO']; 
        }
        return $recorre;
    }
    
    public function getSedes()
    {   
        $datos = $this->select(function (Select $select) {
                    $select->order('NOMBRE_SEDE ASC');                                        
        });     
        //$datos = $this->select();
        $recorre = $datos->toArray();        
                
        return $recorre;
    }
    
}