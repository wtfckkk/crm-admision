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
    
        public function getCombo2($data=array())
    {   
        /*foreach ($data as $valor) {
                                $html = $this->select($valor);                                   
        } 
        $datos = $this->select(array(''));
        $recorre = $datos->toArray();
        $resultado["0"]="Seleccione una Campaña";
        for($i=0;$i<count($recorre);$i++)
        {
          $resultado[$recorre[$i]['id']] = $recorre[$i]['NOMBRE']; 
        }*/
        return $data;
    }
    
    public function getCombo(Adapter $dbAdapter,$data)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select ID_CAMPANA, NOMBRE_CAMPANA from CAMPANAS WHERE ID_CAMPANA IN($data)";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
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