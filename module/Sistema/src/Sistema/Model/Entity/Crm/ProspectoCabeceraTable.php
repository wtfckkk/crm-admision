<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class ProspectoCabeceraTable extends TableGateway
{
    private $RUT;
    private $DV;
    private $NOMBRES;
    private $AP_PATERNO;
    private $AP_MATERNO;
    private $ESTADO;
    
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('PROSPECTO_CABECERA', $adapter, $databaseSchema,$selectResultPrototype);
    }

    private function cargarCampos($datos=array())
    {    
        $this->RUT=$datos["RUT"];
        $this->DV=$datos["DV"];   
        $this->NOMBRES=$datos["NOMBRES"];
        $this->AP_PATERNO=$datos["AP_PATERNO"];
        $this->AP_MATERNO=$datos["AP_MATERNO"];
        $this->ESTADO=$datos["ESTADO"];
        
    }
    
    public function nuevoProsCabecera($data=array())
    {
             self::cargarCampos($data);
             $array=array
             (
                'RUT'=>$this->RUT,
                'DV'=>$this->DV,
                'NOMBRES'=>$this->NOMBRES,
                'AP_PATERNO'=>$this->AP_PATERNO,
                'AP_MATERNO'=>$this->AP_MATERNO,
                'ESTADO'=>$this->ESTADO,
             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
                             
    } 
    
    public function editarProsCabecera($data=array(),$RUT)
    {
             self::cargarCampos($data);
             $array=array
             (
               /* 'RUT'=>$this->RUT,
                'DV'=>$this->DV,*/
                'NOMBRES'=>$this->NOMBRES,
                'AP_PATERNO'=>$this->AP_PATERNO,
                'AP_MATERNO'=>$this->AP_MATERNO,
                'ESTADO'=>$this->ESTADO,
             );               
               $this->update($array,array('RUT'=>$RUT));
    } 
    public function getDatoxRut($rut)
    {
        
        $datos = $this->select(array('RUT'=>$rut));
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