<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class FeedbackTable extends TableGateway
{
    private $ID_OPORTUNIDAD;
    private $OBSERVACION;
    private $ID_TIPO;
    private $ESTADO_GRABADO;
    private $USERNAME;  
    private $FECHA_AGENDAMIENTO;     
    private $FECHA;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('FEEDBACKS', $adapter, $databaseSchema,$selectResultPrototype);
    }

    private function cargarCampos($datos=array())
    {    
        $this->ID_OPORTUNIDAD=$datos["ID_OPORTUNIDAD"];
        $this->OBSERVACION=$datos["OBSERVACION"];   
        $this->ID_TIPO=$datos["ID_TIPO"];
        $this->ESTADO_GRABADO=$datos["ESTADO_GRABADO"];
        $this->USERNAME=$datos["USERNAME"];
        $this->FECHA_AGENDAMIENTO=$datos["FECHA_AGENDAMIENTO"];        
        $fecha = time();
        $this->FECHA=date("Y-m-d H:i:s",$fecha);        
        
    }
    
    public function nuevoFeedback($data=array())
    {
             self::cargarCampos($data);
             $array=array
             ( 
                'ID_OPORTUNIDAD'=>$this->ID_OPORTUNIDAD,
                'OBSERVACION'=>$this->OBSERVACION,
                'ID_TIPO'=>$this->ID_TIPO,
                'ESTADO_GRABADO'=>$this->ESTADO_GRABADO,
                'USERNAME'=>$this->USERNAME,
                'FECHA_AGENDAMIENTO'=>$this->FECHA_AGENDAMIENTO,                
                'FECHA'=>$this->FECHA,
             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
    } 

    
    public function getFeedback($id_oportunidad)
    {
        $datos = $this->select(array('ID_OPORTUNIDAD'=>$id_oportunidad));
        $recorre = $datos->toArray();
                      
        return $recorre;
    }
    
    public function countFeedback(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT count(*) as count FROM FEEDBACKS";                
       $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
       return $result->toArray();
    }
    

    
   
    
}