<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class OportunidadTable extends TableGateway
{
    private $ID_CAMPANA;
    private $RUT;
    private $COD_SEDE;
    private $COD_CARRERA;
    private $JORNADA;
    private $ESTADO;
    private $USERNAME;
    private $FECHA;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('OPORTUNIDADES', $adapter, $databaseSchema,$selectResultPrototype);
    }

    private function cargarCampos($datos=array())
    {    
        $this->ID_CAMPANA=$datos["ID_CAMPANA"];
        $this->RUT=$datos["RUT"];   
        $this->COD_SEDE=$datos["COD_SEDE"];
        $this->COD_CARRERA=$datos["COD_CARRERA"];
        $this->JORNADA=$datos["JORNADA"];
        $this->ESTADO=$datos["ESTADO"];   
        $this->USERNAME=$datos["USERNAME"];           
        $fecha = time();
        $this->FECHA=date("H:i:s d-m-Y",$fecha);        
        
    }
    
    public function nuevaOportunidad($data=array())
    {
             self::cargarCampos($data);
             $array=array
             ( 
                'ID_CAMPANA'=>$this->ID_CAMPANA,
                'RUT'=>$this->RUT,
                'COD_SEDE'=>$this->COD_SEDE,
                'COD_CARRERA'=>$this->COD_CARRERA,
                'JORNADA'=>$this->JORNADA,                
                'ESTADO'=>$this->ESTADO,
                'USERNAME'=>$this->USERNAME,
                'FECHA'=>$this->FECHA,
             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
    } 
    
    public function editarOportunidad($id,$data=array())
    {
             self::cargarCampos($data);
             $array=array
             ( 
                'ID_CAMPANA'=>$this->ID_CAMPANA,
                'RUT'=>$this->RUT,
                'COD_SEDE'=>$this->COD_SEDE,
                'COD_CARRERA'=>$this->COD_CARRERA,
                'JORNADA'=>$this->JORNADA,                
                'ESTADO'=>$this->ESTADO,
                'USERNAME'=>$this->USERNAME,
                'FECHA'=>$this->FECHA,
             );
               $this->update($array,array('ID_OPORTUNIDAD'=>$id));
    } 
    
    public function getOporRutSede(Adapter $dbAdapter,$rut,$sede)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select * from OPORTUNIDADES OP, SEDE_CAMPANA SC, CAMPANA CA WHERE OP.rut ='$rut' and OP.COD_SEDE='$sede' 
       and OP.ID_CAMPANA = SC.ID_CAMPANA AND OP.COD_SEDE = SC.COD_SEDE";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    }
    
        public function getOporRutSede2($id)
    {
        
        $datos = $this->select(array('ID_OPORTUNIDAD'=>$id));
        $recorre = $datos->toArray();              
        return $recorre;
    }           
}