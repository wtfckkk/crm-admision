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

use Sistema\Model\Crm\ProspectoCabeceraTable;
use Sistema\Model\Crm\ProspectoDetalleTable;
use Sistema\Model\Crm\ProsCabeceraDetalleTable;


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
    
        public function buscarutAction()
    {                                     
        $result =  new ViewModel();
        $result->setTerminal(true);
        return $result;                
    }
    
        public function rutAction()
    {
        
         $this->layout('layout/operador');    
        return new ViewModel();
        
        
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
