<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class ProspectoDetalleTable extends TableGateway
{
    private $CORREO;
    private $TELEFONO;
    private $CELULAR;
    private $EMPRESA_ESTABLEC;
    private $DIRECCION;
    private $USERNAME_ACTUALIZACION;
    private $FECHA;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('PROSPECTO_DETALLE', $adapter, $databaseSchema,$selectResultPrototype);
    }

    private function cargarCampos($datos=array())
    {    
        $this->CORREO=$datos["CORREO"];
        $this->TELEFONO=$datos["TELEFONO"];   
        $this->CELULAR=$datos["CELULAR"];
        $this->EMPRESA_ESTABLEC=$datos["EMPRESA_ESTABLEC"];
        $this->DIRECCION=$datos["DIRECCION"];
        $this->USERNAME_ACTUALIZACION=$datos["USERNAME_ACTUALIZACION"];
        $fecha = time();
        $this->FECHA=date("Y-m-d H:i:s",$fecha);        
        
    }
    
    public function nuevoProsDetalle($data=array())
    {
             self::cargarCampos($data);
             $array=array
             ( 
                'CORREO'=>$this->CORREO,
                'TELEFONO'=>$this->TELEFONO,
                'CELULAR'=>$this->CELULAR,
                'EMPRESA_ESTABLEC'=>$this->EMPRESA_ESTABLEC,
                'DIRECCION'=>$this->DIRECCION,
                'USERNAME_ACTUALIZACION'=>$this->USERNAME_ACTUALIZACION,
                'FECHA'=>$this->FECHA,
             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
    } 
    
    public function editarProsDetalle($data=array())
    {
             self::cargarCampos($data);
             $array=array
             (  'ID_DETALLE'=>$id_detalle,
                'CORREO'=>$this->CORREO,
                'TELEFONO'=>$this->TELEFONO,
                'CELULAR'=>$this->CELULAR,
                'EMPRESA_ESTABLEC'=>$this->EMPRESA_ESTABLEC,
                'DIRECCION'=>$this->DIRECCION,
                'USERNAME_ACTUALIZACION'=>$this->USERNAME_ACTUALIZACION,
             );
               $this->update($array,array('id'=>$id));
    } 
    public function getDetalle($id_detalle)
    {
        $datos = $this->select(array('ID_DETALLE'=>$id_detalle));
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