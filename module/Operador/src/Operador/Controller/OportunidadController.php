<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Operador\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Sistema\Model\Entity\Crm\ProspectoCabeceraTable;
use Sistema\Model\Entity\Crm\ProspectoDetalleTable;
use Sistema\Model\Entity\Crm\ProsCabeceraDetalleTable;
use Sistema\Model\Entity\Crm\UsuarioSedeTable;
use Sistema\Model\Entity\Crm\SedeCampanaTable;
use Sistema\Model\Entity\Crm\CampanaTable;


class OportunidadController extends AbstractActionController
{
    public function indexAction()
    {
        
        $this->layout('layout/operador');
        return new ViewModel();
        
        
    }
    
        public function nuevaAction()
    {
        
         $this->layout('layout/operador');    
        return new ViewModel();
        
        
    }
        public function rutAction()
    {
        
         $this->layout('layout/operador');    
        return new ViewModel();
        
        
    }
    
    public function buscarutAction()
    {                                             
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');        
        //Instancia de Tablas
        $pcabecera = new ProspectoCabeceraTable($this->dbAdapter); 
        $pdetalle     = new ProspectoDetalleTable($this->dbAdapter);       
        //Buscamos prospecto       
        $prospecto = $pcabecera->getDatoxRut($lista['RUT']);                             
         //Validamos si prospecto existe
         if(count($prospecto)>0){
            //Buscamos detalle de Prospecto
            $pcabedetalle = new ProsCabeceraDetalleTable($this->dbAdapter);
            $cab_detalle   = $pcabedetalle->getIdDetalle($prospecto[0]['RUT']);
                //Cargamos data para grilla
                $html = "";
                for ($i=0;$i<count($cab_detalle);$i++){
	               $detalletable =  $pdetalle->getDetalle($cab_detalle[$i]['ID_DETALLE']);  
                        for ($j=0;$j<count($detalletable);$j++){
                            $html .= '<tr>';
                            foreach ($detalletable[$j] as $key => $valor) {
                                $html = $html.'<td>'.$valor.'</td>';                                   
                                } 
                            $html .= '</tr>';       
                        }
                }
                $html .= "</tr>";
            //Validamos historico de detalles
                if(count($cab_detalle)>1){
                    $cab_detalle[0] = max($cab_detalle);
                }
                        
            $detalle = $pdetalle->getDetalle($cab_detalle[0]['ID_DETALLE']);
            
            //Retornamos valores del prospecto            
            $result = new JsonModel(array('existe'=>'si','prospecto'=>$prospecto,'detalle'=>$detalle,'tabla'=>$html));
            $result->setTerminal(true);
            return $result;             
         }
         else{
            //Retornamos mensaje de no existencia a la vista
            $descripcion = "Prospecto no registrado en el sistema";                         
            $result = new JsonModel(array('desc'=>$descripcion));
            $result->setTerminal(true);
            return $result;             
         }                          
    }
    
    public function actualizardatosAction()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
            
           //Tablas
           $pcabecera    = new ProspectoCabeceraTable($this->dbAdapter);
           $pdetalle     = new ProspectoDetalleTable($this->dbAdapter);
           $pcabedetalle = new ProsCabeceraDetalleTable($this->dbAdapter);
           //Calculamos DV
           $lista['DV'] =  \Utils::calculaDV($lista['RUT']); 
            //Validamos si existe prospecto       
           /* $existe = $pcabecera->getDatoxRut($lista['RUT']);            
            if(count($existe)>0)
               {  
                //Insertamos en tabla ProspectoDetalle            
                $id_detalle = $pdetalle->nuevoProsDetalle($lista);
                
                //Insertamos en tabla ProspectoDetalle            
                $pcabedetalle->nuevoProsCabeceraDet($lista['RUT'],$id_detalle);                                
            
                //Retornamos a la Vista
                $desc = "Se <strong>actualizaron datos de prospecto</strong> correctamente";
                }
            else
                {                    
                //Insertamos nuevo prospecto en tabla ProspectoCabecera            
                $id = $pcabecera->nuevoProsCabecera($lista);
                //Insertamos detalle de prospecto en tabla ProspectoDetalle            
                $id_detalle = $pdetalle->nuevoProsDetalle($lista);
                //Insertamos en tabla ProspectoDetalle            
                $pcabedetalle->nuevoProsCabeceraDet($lista['RUT'],$id_detalle);                                       
                 
                $desc = "Se ha ingresado <strong>nuevo prospecto</strong> correctamente";    
                }*/
                        
            //Retornamos a la Vista            
            $result = new JsonModel(array('status'=>'ok','descripcion'=>$lista['DV']));
            $result->setTerminal(true); 
            return $result; 
    }
    
    public function combospaso2Action()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
            
           //Tablas
           $usersede  = new UsuarioSedeTable($this->dbAdapter);
           $sedecamp  = new SedeCampanaTable($this->dbAdapter);
           $campana  = new CampanaTable($this->dbAdapter);
           
           //Consultamos codigo de sedes por usuario       
           $codesede = $usersede->getSede($lista['user']);
           //Consultamos sede por usuario       
           $idcampanas = $sedecamp->getIDCampana($codesede[0]['COD_SEDE']);
           //Consultamos sedes para combo       
           $combocamp = $campana->getCombo($this->dbAdapter,implode(',',$idcampanas));
           
          // $test= implode(',',$idcampanas);
           
           
        
            
            //Retornamos a la Vista            
            $result = new JsonModel(array('status'=>'ok','campanas'=>$combocamp));
            $result->setTerminal(true); 
            return $result; 
        
    }
    
        public function rut2Action()
    {
        
         $this->layout('layout/operador');    
        return new ViewModel();
        
        
    }
    
        public function rut3Action()
    {
        
         $this->layout('layout/operador');    
        $result =  new ViewModel();
        $result->setTerminal(true);
        return $result; ;
        
        
    }
    
        public function campanaAction()
    {
        
         $this->layout('layout/operador');    
        return new ViewModel();
        
        
    }
    
        public function campana2Action()
    {
        
         $this->layout('layout/operador');    
        return new ViewModel();
        
        
    }
}
