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
        $this->FECHA=date("Y-m-d H:i:s ",$fecha);        
        
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
    
    public function editarEstado($id,$estado)
    {             
               $this->update(array('ESTADO'=>$estado),array('ID_OPORTUNIDAD'=>$id));
    }  
    
    public function getOporRutSede(Adapter $dbAdapter,$rut,$sede)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select OP.ID_OPORTUNIDAD, SE.NOMBRE_SEDE, CAR.NOMBRE_CARRERA, OP.JORNADA, OP.ESTADO, (SELECT FE.OBSERVACION FROM feedbacks fe WHERE FE.ID_FEEDBACK = (select MAX(FE.ID_FEEDBACK) from FEEDBACKS FE where FE.ID_OPORTUNIDAD = OP.ID_OPORTUNIDAD))  AS FEEDBACK 
                from OPORTUNIDADES OP, CAMPANAS CA, SEDES SE, CARRERAS CAR 
                WHERE OP.ID_CAMPANA = CA.ID_CAMPANA
                AND CA.ACTIVO = 's'
                AND OP.COD_SEDE = SE.COD_SEDE
                AND OP.COD_CARRERA = CAR.COD_CARRERA
                AND OP.rut ='$rut' 
                AND OP.COD_SEDE='$sede' ";
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    }

    public function getOportunidad($id)
    {
        
        $datos = $this->select(array('ID_OPORTUNIDAD'=>$id));
        $recorre = $datos->toArray();              
        return $recorre;
    }
    
    public function getdetOportunidad(Adapter $dbAdapter,$id_oportunidad)
    {
       $this->dbAdapter = $dbAdapter;
       $query="SELECT OP.ID_OPORTUNIDAD, CA.NOMBRE_CAMPANA, SE.NOMBRE_SEDE, CAR.NOMBRE_CARRERA, OP.JORNADA, OP.ESTADO, (SELECT FE.OBSERVACION FROM feedbacks fe WHERE FE.ID_FEEDBACK = (select MAX(FE.ID_FEEDBACK) from FEEDBACKS FE where FE.ID_OPORTUNIDAD = OP.ID_OPORTUNIDAD)) AS FEEDBACK  FROM OPORTUNIDADES OP, CAMPANAS CA, SEDES SE, CARRERAS CAR
            WHERE OP.ID_CAMPANA = CA.ID_CAMPANA
            AND OP.COD_SEDE = SE.COD_SEDE
            AND OP.COD_CARRERA = CAR.COD_CARRERA
            AND ID_OPORTUNIDAD = '$id_oportunidad'" ;
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    }
    
     public function getOporCampana(Adapter $dbAdapter,$cod_sede,$id_campana)
    {
       $this->dbAdapter = $dbAdapter;
       $query="SELECT * from OPORTUNIDADES WHERE ESTADO <> 'Cerrada'
               and COD_SEDE = '$cod_sede'
               and ID_CAMPANA = '$id_campana'" ;
                
        $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
        
        return $result->toArray();
    }
    
    public function countOportunidades(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "SELECT count(*) as count FROM OPORTUNIDADES";                
       $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
       return $result->toArray();
    }
    
    public function getAgendamientos(Adapter $dbAdapter,$fecha_inicial,$fecha_final,$usuario)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select distinct a.id_oportunidad, a.id_campana, 
                    dbo.SacaNombreCampana(a.id_campana) nombre_campana, a.rut, b.dv,
                    upper(b.nombres) nombres, upper(b.ap_paterno) ap_paterno, 
                    upper(b.ap_materno) ap_materno, a.cod_sede,
                    dbo.SacaNombreSede(a.cod_sede) nombre_sede, a.cod_carrera, 
                    dbo.SacaNombreCarrera(a.cod_carrera) nombre_carrera,
                    a.jornada, a.estado, a.fecha, dbo.SacaEstadoUMAS(a.rut) matriculado_umas, 
                    c.observacion, c.username operador,
                    c.fecha_agendamiento
                    from oportunidades a, prospecto_cabecera b, feedbacks c
                    where a.rut = b.rut
                    and a.id_oportunidad = c.id_oportunidad
                    and c.id_tipo = 3
                    and c.fecha_agendamiento between '$fecha_inicial' and '$fecha_final'
                    and a.cod_sede in (select cod_sede from usuario_sede where username = 
                    '$usuario')
                    order by a.id_oportunidad asc";                
       $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
       return $result->toArray();
    }
    
    public function getOportunidadesFull(Adapter $dbAdapter)
    {
       $this->dbAdapter = $dbAdapter;
       $query = "select distinct a.id_oportunidad, a.id_campana, 
                    dbo.SacaNombreCampana(a.id_campana) nombre_campana, a.rut, b.dv,
                    upper(b.nombres) nombres, upper(b.ap_paterno) ap_paterno, 
                    upper(b.ap_materno) ap_materno, a.cod_sede,
                    dbo.SacaNombreSede(a.cod_sede) nombre_sede, a.cod_carrera, 
                    dbo.SacaNombreCarrera(a.cod_carrera) nombre_carrera,
                    a.jornada, a.estado, a.fecha, dbo.SacaEstadoUMAS(a.rut) matriculado_umas
                    from oportunidades a, prospecto_cabecera b
                    where a.rut = b.rut
                    order by id_oportunidad asc";                
       $result=$this->dbAdapter->query($query,Adapter::QUERY_MODE_EXECUTE);
       return $result->toArray();
    }
    
               
}