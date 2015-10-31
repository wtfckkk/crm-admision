<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class CampanaTable extends TableGateway
{
    private $NOMBRE_CAMPANA;
    private $ID_TIPO;
    private $ACTIVO;
    private $USERNAME;
    private $ANO_ACADEMICO;
    private $FECHA;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('CAMPANAS', $adapter, $databaseSchema,$selectResultPrototype);
    }

    private function cargarCampos($datos=array())
    {    
        $this->NOMBRE_CAMPANA=$datos["NOMBRE_CAMPANA"];
        $this->ID_TIPO=$datos["ID_TIPO"];   
        $this->ACTIVO=$datos["ACTIVO"];
        $this->USERNAME=$datos["USERNAME"];
        $this->ANO_ACADEMICO=$datos["ANO_ACADEMICO"];        
        $fecha = time();
        $this->FECHA=date("Y-m-d H:i:s",$fecha);        
        
    }
    
    public function nuevaCampana($data=array())
    {
             self::cargarCampos($data);
             $array=array
             ( 
                'NOMBRE_CAMPANA'=>$this->NOMBRE_CAMPANA,
                'ID_TIPO'=>$this->ID_TIPO,
                'ACTIVO'=>$this->ACTIVO,
                'USERNAME'=>$this->USERNAME,
                'ANO_ACADEMICO'=>$this->ANO_ACADEMICO,                
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
    
    public function borraCampana($id_campana)
    {             
             $array=array('ID_CAMPANA'=>$id_campana);
             
             $this->delete($array);
    }
    
    public function getCombo(Adapter $dbAdapter,$data)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select ID_CAMPANA, NOMBRE_CAMPANA from CAMPANAS WHERE ID_CAMPANA IN($data)";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    }
    
    public function getComboFull()
    {
        $datos = $this->select(array('activo'=>'si'));
        $recorre = $datos->toArray();
                      
        return $recorre;
    }
    
    public function getComboSede(Adapter $dbAdapter,$sede)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select CA.ID_CAMPANA, CA.NOMBRE_CAMPANA from SEDE_CAMPANA SC, CAMPANAS CA
                WHERE CA.ID_CAMPANA = SC.ID_CAMPANA
                AND   CA.ACTIVO = 's'
                and SC.COD_SEDE = '$sede'";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    }
    
        public function getCampanas(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT CA.ID_CAMPANA, CA.NOMBRE_CAMPANA, TC.DESC_TIPO, CA.ACTIVO, (select SEDES.NOMBRE_SEDE FROM SEDES WHERE SEDES.COD_SEDE=SC.COD_SEDE) AS NOMBRE_SEDE, SC.COD_SEDE
                FROM CAMPANAS CA, TIPO_CAMPANAS TC, SEDE_CAMPANA SC
                WHERE CA.ID_TIPO = TC.ID_TIPO
                AND CA.ID_CAMPANA = SC.ID_CAMPANA";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    } 
    public function getDetalle($id_detalle)
    {
        $datos = $this->select(array('ID_DETALLE'=>$id_detalle));
        $recorre = $datos->toArray();
                      
        return $recorre;
    }            
    public function getCampanaXsede(Adapter $dbAdapter,$cod_sede)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT ID_CAMPANA, NOMBRE_CAMPANA FROM CAMPANAS 
                WHERE ID_CAMPANA IN (select ID_CAMPANA from SEDE_CAMPANA where COD_SEDE = '$cod_sede')";                
       $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
       return $result->toArray();
    }
    
    public function countCampana(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT count(*) as count FROM CAMPANAS where ACTIVO = 's'";                
       $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
       return $result->toArray();
    }
}