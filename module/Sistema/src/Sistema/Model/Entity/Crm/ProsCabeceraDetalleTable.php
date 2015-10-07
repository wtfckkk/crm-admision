<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class ProsCabeceraDetalleTable extends TableGateway
{
    private $RUT;
    private $ID_DETALLE;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('PROSP_CABECERA_DETALLE', $adapter, $databaseSchema,$selectResultPrototype);
    }

    public function nuevoProsCabeceraDet($RUT,$ID_DETALLE)
    {
             self::cargarCampos($data);
             $array=array
             (
                'RUT'=>$this->RUT,
                'ID_DETALLE'=>$this->ID_DETALLE,

             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
    } 
    public function getIdDetalle($rut)
    {
        
        $datos = $this->select(array('rut'=>$rut));
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