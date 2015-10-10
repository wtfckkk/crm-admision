<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class SedeCarreraTable extends TableGateway
{    
    private $COD_SEDE;
    private $COD_CARRERA;
    private $JORNADA;
    private $ANO_ACADEMICO;
    private $SEM_ACADEMICO;
    private $ACTIVO;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('SEDE_CARRERA', $adapter, $databaseSchema,$selectResultPrototype);
    }
    
    private function cargarCampos($datos=array())
    {    
        $this->COD_SEDE=$datos['COD_SEDE'];
        $this->COD_CARRERA=$datos['COD_CARRERA'];   
        $this->JORNADA=$datos['JORNADA'];
        $this->ANO_ACADEMICO=$datos['ANO_ACADEMICO'];
        $this->SEM_ACADEMICO=$datos['SEM_ACADEMICO'];        
        
    }
    
    public function nuevaCarreraSede($data=array())
    {        
             self::cargarCampos($data);
             $array=array
             (
                'COD_SEDE'=>$COD_SEDE,
                'COD_CARRERA'=>$COD_CARRERA,
                'JORNADA'=>$JORNADA,
                'ANO_ACADEMICO'=>$ANO_ACADEMICO,
                'SEM_ACADEMICO'=>$SEM_ACADEMICO,                

             );
               $this->insert($array);
               $id = $this->lastInsertValue;
               return $id;
    } 
    
    public function getIDCarrera($codesede)
    {
        
        $datos = $this->select(array('COD_SEDE'=>$codesede));
        $recorre = $datos->toArray();
         for($i=0;$i<count($recorre);$i++)
        {
          $result[$i] = $recorre[$i]['COD_CARRERA']; 
        }             
        return $result;
    }
    
    public function getJornada($codecarrera,$codesede)
    {
        
        $datos = $this->select(array('COD_CARRERA'=>$codecarrera,'COD_SEDE'=>$codesede));
        $recorre = $datos->toArray();
         for($i=0;$i<count($recorre);$i++)
        {
          $result[$i] = $recorre[$i]['JORNADA']; 
        }             
        return $result;
    }
    
}