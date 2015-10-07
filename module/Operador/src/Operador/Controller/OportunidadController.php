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
        //Buscamos prospecto       
        $prospecto = $pcabecera->getDatoxRut($lista['rut']);                             
         //Validamos si prospecto existe
         if(count($prospecto)>0){
            //Buscamos detalle de Prospecto
            $pcabedetalle = new ProsCabeceraDetalleTable($this->dbAdapter);
            $cab_detalle   = $pcabedetalle->getIdDetalle($prospecto[0]['RUT']);
            $pdetalle     = new ProspectoDetalleTable($this->dbAdapter);
            $detalle      = $pdetalle->getDetalle($cab_detalle[0]['ID_DETALLE']);
            
            //Retornamos valores del prospecto            
            $result = new JsonModel(array('existe'=>'si','prospecto'=>$prospecto,'detalle'=>$detalle));
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
            
            //Validamos si existe prospecto       
            $existe = $pcabecera->getDatoxRut($lista['RUT']);            
            if(isset($existe))
               {                           
                //Update en tabla ProspectoCabecera            
                $pcabecera->editarProsCabecera($lista,$lista['RUT']);
            
                //Update en tabla ProspectoDetalle            
                $id_detalle = $pdetalle->nuevoProsDetalle($lista);
                
                //Update en tabla ProspectoDetalle            
                $id_detalle = $pcabedetalle->nuevoProsCabeceraDet($lista['RUT'],$id_detalle);
            
                //Retornamos a la Vista
                $desc = "Se ha ingresado <strong>nuevo prospecto</strong> exitosa";
                $result = new JsonModel(array('status'=>'ok','descripcion'=>$desc));
                $result->setTerminal(true);  
                }else
                {
                    
                //Insertamos en tabla ProspectoCabecera            
                $pcabecera->nuevoProsCabecera($lista);
            
                //Insertamos en tabla ProspectoDetalle            
                $id_detalle = $pdetalle->nuevoProsDetalle($lista);
                
                //Insertamos en tabla ProspectoDetalle            
                $id_detalle = $pcabedetalle->nuevoProsCabeceraDet($lista['RUT'],$id_detalle);
            
                //Retornamos a la Vista
                $desc = "Se ha ingresado <strong>nuevo prospecto</strong> exitosa";
                $result = new JsonModel(array('status'=>'ok','descripcion'=>$desc));
                $result->setTerminal(true);                
                    
                }

        
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
