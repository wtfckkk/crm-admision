<?php

namespace Sistema\Model\Entity\Crm;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;

class UsuarioSedeTable extends TableGateway
{
    private $USERNAME;
    private $COD_SEDE;
    
    public $dbAdapter;
 
    public function __construct(Adapter $adapter = null, $databaseSchema = null, ResultSet $selectResultPrototype = null)
    {
        return parent::__construct('USUARIO_SEDE', $adapter, $databaseSchema,$selectResultPrototype);
    }

    public function nuevoUsuSede($user,$cod_sede)
    {             
             $array=array
             (
                'USERNAME'=>$user,
                'COD_SEDE'=>$cod_sede,

             );
               $this->insert($array);
    } 
    public function getSede($user)
    {        
        $datos = $this->select(array('USERNAME'=>$user));
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
    
    public function getUsuariosxSede(Adapter $dbAdapter,$perfil,$cod_sede)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select U.NOMBRE_FULL, U.USERNAME, UP.ID_PERFIL, SE.NOMBRE_SEDE FROM USUARIOS U, USUARIO_PERFIL UP, USUARIO_SEDE US, SEDES SE
                WHERE U.USERNAME = UP.USERNAME
                AND U.USERNAME = US.USERNAME
                AND SE.COD_SEDE = US.COD_SEDE
                AND UP.ID_PERFIL = '$perfil'
                and US.COD_SEDE = '$cod_sede'";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    public function getUsuarioxSede(Adapter $dbAdapter,$user,$perfil,$cod_sede)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select U.NOMBRE_FULL, U.USERNAME, UP.ID_PERFIL, SE.NOMBRE_SEDE FROM USUARIOS U, USUARIO_PERFIL UP, USUARIO_SEDE US, SEDES SE
                WHERE U.USERNAME = UP.USERNAME
                AND U.USERNAME = US.USERNAME
                AND SE.COD_SEDE = US.COD_SEDE
                AND UP.ID_PERFIL = '$perfil'
                and US.COD_SEDE = '$cod_sede'
                and U.USERNAME = '$user'";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        return $result->toArray();
    }
    
    public function borraUsuario($user)
    {             
             $array=array('USERNAME'=>$user);
             
             $this->delete($array);
    }
    

    
   
    
}