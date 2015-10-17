<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class UsuarioTable extends TableGateway
{
    
    private $USERNAME;
    private $NOMBRE_FULL;
    private $PASSWORD;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('USUARIOS', $adapter, $databaseSchema,$selectResultPrototype);
    }

    private function cargarCampos($datos=array())
    {    
        $this->USERNAME=$datos["USERNAME"];
        $this->NOMBRE_FULL=$datos["NOMBRE_FULL"];   
        $this->PASSWORD=$datos["PASSWORD"];      
        
    }
    
    public function nuevoUsuario($data=array())
    {
             self::cargarCampos($data);
             $array=array
             ( 
                'USERNAME'=>$this->USERNAME,
                'NOMBRE_FULL'=>$this->NOMBRE_FULL,
                'PASSWORD'=>$this->PASSWORD,                
             );
               $this->insert($array);
    }
    public function editarUsuario($user,$pass)
    {             
             $array=array
             (                                
                'PASSWORD'=>$pass,   
             );
               $this->update($array,array('USERNAME'=>$user));                  
    } 
    public function getUser($user)
    {
        
        $datos = $this->select(array('USERNAME'=>$user));
        $recorre = $datos->toArray();
                      
        return $recorre;
    }
    public function getDatos2()
    {
        
        $datos = $this->select();
        $recorre = $datos->toArray();
                      
        return $recorre;
    }
    
    
    public function getUsuario(Adapter $dbAdapter,$user,$pass)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select U.USERNAME, U.NOMBRE_FULL, S.COD_SEDE, SE.NOMBRE_SEDE 
                 from USUARIOS U, USUARIO_SEDE S, SEDES SE 
                 where S.COD_SEDE = SE.COD_SEDE 
                 AND U.USERNAME=S.USERNAME 
                 AND U.USERNAME='$user' 
                 and U.PASSWORD='$pass'";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    
        public function borraUsuario($user)
    {             
             $array=array('USERNAME'=>$user);
             
             $this->delete($array);
    }
    
   
    
}
