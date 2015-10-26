<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admincentral\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Session\Container;

use Sistema\Model\Entity\Crm\ProspectoCabeceraTable;
use Sistema\Model\Entity\Crm\ProspectoDetalleTable;
use Sistema\Model\Entity\Crm\ProsCabeceraDetalleTable;
use Sistema\Model\Entity\Crm\UsuarioSedeTable;
use Sistema\Model\Entity\Crm\SedeCampanaTable;
use Sistema\Model\Entity\Crm\CampanaTable;
use Sistema\Model\Entity\Crm\CarrerasTable;
use Sistema\Model\Entity\Crm\SedeCarreraTable;
use Sistema\Model\Entity\Crm\OportunidadTable;
use Sistema\Model\Entity\Crm\TipoFeedbackTable;
use Sistema\Model\Entity\Crm\FeedbackTable;



class CargasController extends AbstractActionController
{
    public function indexAction()
    {
        
        $this->layout('layout/admincentral');
        return new ViewModel();
        
        
    }
    
    public function prospectosAction()
    {                        
        $this->layout('layout/admincentral');
        $result = new ViewModel();        
        return $result;
        
        
    }
    
    public function checkprospectosAction()
    {
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');  
        
        //Instancias                             
        $pcabecera  = new ProspectoCabeceraTable($this->dbAdapter);         
        
        //Definimos ruta DEBE EXISTIR PREVIAMENTE
        $ruta = $_SERVER['DOCUMENT_ROOT'].'/crm/excel/tmp';
        //Obtenemos y guardamos File    
        $file = $this->params()->fromFiles();                                
        $adapterFile = new \Zend\File\Transfer\Adapter\Http();
        $adapterFile->setDestination($ruta);
        $adapterFile->receive($file['file-0']['name']);            
        
        //Buscamos el file
        $inputFileName = $ruta."/".$file['file-0']['name'];
        // Validamos Extension del file
        $trozos = explode(".", $inputFileName);
         if (end($trozos) != "csv" && end($trozos) != "xls" && end($trozos) != "xlsx"){
                unlink($inputFileName);
                 $result = new JsonModel(array('status'=>'nok','descr'=>'Archivo Inv&aacute;lido'));
                                $result->setTerminal(true);
                                return $result;
                
         }
        //Obtenemos clase PHPExcel
            $objPHPExcel = new \PHPExcel();
                                                             
            //Llamamos Clase PHPExcel
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objReader->setReadDataOnly(TRUE);
            $objPHPExcel = $objReader->load($inputFileName);    
            //Obtenemos Hoja de trabajo
            $objWorksheet = $objPHPExcel->getActiveSheet();
            // Obtenemos los rangos de celdas de planilla excel
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = (\PHPExcel_Cell::columnIndexFromString($highestColumn))-1; // e.g. 5  
            //Asociamos data con Indices de BBDD
            $indices = array('RUT'); 
            
            //Recorremos excel                             
            for ($row = 3; $row <= $highestRow; ++$row) {                                                           
                    
                   $lista[$row][0] = $objWorksheet->getCellByColumnAndRow(0,$row)->getValue();                                                                     
               
                    //Agregamos indices a array 
                    error_reporting(E_ERROR);                   
                    $data = array_combine($indices,$lista[$row]);
                            if (!$data){                        
                                $result = new JsonModel(array('status'=>'error','descripcion'=>"ERROR! Archivo no corresponde a ".ucfirst($flag)));
                                $result->setTerminal(true);
                                return $result;                                         
                            }
                    $existe = $pcabecera->getDatoxRut($data['RUT']);                             
                    //Validamos si rut existe en BBDD                            
                    if(count($existe)>0){
                                $result = new JsonModel(array('status'=>'nok','descr'=>'Rut '.$data['RUT'].' ya existe en el sistema'));
                                $result->setTerminal(true);
                                return $result;
                    }         
                                                
                  } 
        unlink($inputFileName);                           
        //Retornamos a la vista            
        $result = new JsonModel(array('status'=>'ok','data'=>$objPHPExcel));
        $result->setTerminal(true);
        return $result;
                            
                              
    }
    
    public function cargaprospectosAction()
    {               
           
           //Conectamos con BBDD
           $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');  
           
           //Instancias                  
           $sid = new Container('base');
           $pcabecera     = new ProspectoCabeceraTable($this->dbAdapter);  
           $pdetalle      = new ProspectoDetalleTable($this->dbAdapter);
           $pcabedetalle  = new ProsCabeceraDetalleTable($this->dbAdapter);
            
           //Definimos ruta DEBE EXISTIR PREVIAMENTE
           $ruta = $_SERVER['DOCUMENT_ROOT'].'/crm/excel';                        
                                              
           //Obtenemos y guardamos File    
            $file = $this->params()->fromFiles();                                
            $adapterFile = new \Zend\File\Transfer\Adapter\Http();
            $adapterFile->setDestination($ruta);
            $adapterFile->receive($file['file-0']['name']);
            
            //Buscamos el file
            $inputFileName = $ruta."/".$file['file-0']['name'];
                                
            //Obtenemos clase PHPExcel
            $objPHPExcel = new \PHPExcel();
                                                             
            //Llamamos Clase PHPExcel
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objReader->setReadDataOnly(TRUE);
            $objPHPExcel = $objReader->load($inputFileName);
            //Obtenemos Hoja de trabajo
            $objWorksheet = $objPHPExcel->getActiveSheet();
            // Obtenemos los rangos de celdas de planilla excel
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = (\PHPExcel_Cell::columnIndexFromString($highestColumn))-1; // e.g. 5
            //Asociamos data con Indices de BBDD
            $indices = array('RUT','DV','NOMBRES','AP_PATERNO','AP_MATERNO','CORREO','TELEFONO','CELULAR','EMPRESA_ESTABLEC','DIRECCION');            
            $html="";                        
            //Recorremos excel                             
            for ($row = 3; $row <= $highestRow; ++$row) {                
                 
                 for ($col = 0; $col <= $highestColumnIndex; ++$col) {         
                    
                   $lista[$row][$col] = $objWorksheet->getCellByColumnAndRow($col,$row)->getValue();                                                                     
                }
                    //Agregamos indices a array 
                    error_reporting(E_ERROR);                   
                    $data = array_combine($indices,$lista[$row]);
                            if (!$data){                        
                                $result = new JsonModel(array('status'=>'error','descripcion'=>"ERROR! Archivo no corresponde a ".ucfirst($flag)));
                                $result->setTerminal(true);
                                return $result;
                            }                        
                    //Agregamos estado y usuario al array
                    $data['ESTADO'] = "Cargado";
                    $data['USERNAME_ACTUALIZACION'] = $sid->offsetGet('usuario');     
                                    
                    //Insertamos nuevo prospecto en PROSPECTO_CABECERA                               
                    $pcabecera->nuevoProsCabecera($data);                        
                    //Insertamos detalle de prospecto en tabla ProspectoDetalle            
                    $id_detalle = $pdetalle->nuevoProsDetalle($data);
                    //Insertamos en tabla ProspectoDetalle            
                    $pcabedetalle->nuevoProsCabeceraDet($data['RUT'],$id_detalle);   
                    //Agregamos fila para mostrar en la vista
                    $html.="<tr>";
                    $html.="<td>".number_format($data['RUT'],-3,"",".")."-".$data['DV']
                         ."</td><td>".$data['NOMBRES']
                         ."</td><td>".$data['AP_PATERNO']." ".$data['AP_MATERNO']."</td><td>".$data['CORREO']
                         ."</td><td>".$data['TELEFONO']."</td><td>".$data['EMPRESA_ESTABLEC']
                         ."</td><td>".$data['ESTADO'];
                    $html.="</tr>";                              
            } 
        //Cambiamos nombre y hacemos tar a file para historico                      
        rename($inputFileName,$inputFileName.'.'.date('Ymd').str_replace(":","",date('H:i:s')));
        //Retornamos a la vista 
        $descr = "Se han ingresado <strong>".($highestRow-2)."</strong> prospectos exitosamente";   
        $this->layout('layout/admincentral');
        $result = new JsonModel(array('status'=>'ok','html'=>$html,'descr'=>$descr));
        return $result;
        
        
    }
    
    public function oportunidadesAction()
    {
        
        $this->layout('layout/admincentral');
        return new ViewModel();
        
        
    }
    
        public function checkoportunidadesAction()
    {
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');  
        
        //Instancias                             
        $pcabecera     = new ProspectoCabeceraTable($this->dbAdapter);         
        
        //Definimos ruta DEBE EXISTIR PREVIAMENTE
        $ruta = $_SERVER['DOCUMENT_ROOT'].'/crm/excel/tmp';
        //Obtenemos y guardamos File    
        $file = $this->params()->fromFiles();                                
        $adapterFile = new \Zend\File\Transfer\Adapter\Http();
        $adapterFile->setDestination($ruta);
        $adapterFile->receive($file['file-0']['name']);            
        
        //Buscamos el file
        $inputFileName = $ruta."/".$file['file-0']['name'];
        
        // Validamos Extension del file
        $trozos = explode(".", $inputFileName);
         if (end($trozos) != "csv" && end($trozos) != "xls" && end($trozos) != "xlsx"){
                 $result = new JsonModel(array('status'=>'nok','descr'=>'Archivo Inv&aacute;lido'));
                                $result->setTerminal(true);
                                return $result;
                
         }
        
        //Obtenemos clase PHPExcel
            $objPHPExcel = new \PHPExcel();
                                                             
            //Llamamos Clase PHPExcel
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objReader->setReadDataOnly(TRUE);
            $objPHPExcel = $objReader->load($inputFileName);
            //Obtenemos Hoja de trabajo
            $objWorksheet = $objPHPExcel->getActiveSheet();
            // Obtenemos los rangos de celdas de planilla excel
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = (\PHPExcel_Cell::columnIndexFromString($highestColumn))-1; // e.g. 5  
            //Asociamos data con Indices de BBDD
            $indices = array('RUT'); 
            
            //Recorremos excel                             
            for ($row = 3; $row <= $highestRow; ++$row) {                                                           
                    
                   $lista[$row][1] = $objWorksheet->getCellByColumnAndRow(1,$row)->getValue();                                                                     
               
                    //Agregamos indices a array 
                    error_reporting(E_ERROR);                   
                    $data = array_combine($indices,$lista[$row]);
                            if (!$data){                        
                                $result = new JsonModel(array('status'=>'error','descripcion'=>"El archivo no corresponde a Maestro de Prospectos"));
                                $result->setTerminal(true);
                                return $result;                                         
                            }
                    $existe = $pcabecera->getDatoxRut($data['RUT']);                             
                    //Validamos si rut existe en BBDD                            
                    if(count($existe)<1){
                                $result = new JsonModel(array('status'=>'nok','descr'=>'Rut '.$data['RUT'].' no existe en el sistema'));
                                $result->setTerminal(true);
                                return $result;
                    }         
                                                
                  } 
        unlink($inputFileName);                            
        //Retornamos a la vista            
        $result = new JsonModel(array('status'=>'ok'));
        $result->setTerminal(true);
        return $result;
                            
                              
    }
    
     public function cargaoportunidadesAction()
    {               
           
           //Conectamos con BBDD
           $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');  
           
           //Instancias                  
           $sid = new Container('base');
           $oportunidad   = new OportunidadTable($this->dbAdapter);  
           $feedback      = new FeedbackTable($this->dbAdapter);
           $pcabecera     = new ProspectoCabeceraTable($this->dbAdapter);   
            
           //Definimos ruta DEBE EXISTIR PREVIAMENTE
           $ruta = $_SERVER['DOCUMENT_ROOT'].'/crm/excel';                        
                                              
           //Obtenemos y guardamos File    
            $file = $this->params()->fromFiles();                                
            $adapterFile = new \Zend\File\Transfer\Adapter\Http();
            $adapterFile->setDestination($ruta);
            $adapterFile->receive($file['file-0']['name']);
            
            //Buscamos el file
            $inputFileName = $ruta."/".$file['file-0']['name'];
                                
            //Obtenemos clase PHPExcel
            $objPHPExcel = new \PHPExcel();
                                                             
            //Llamamos Clase PHPExcel
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objReader->setReadDataOnly(TRUE);
            $objPHPExcel = $objReader->load($inputFileName);
            //Obtenemos Hoja de trabajo
            $objWorksheet = $objPHPExcel->getActiveSheet();
            // Obtenemos los rangos de celdas de planilla excel
            $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
            $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
            $highestColumnIndex = (\PHPExcel_Cell::columnIndexFromString($highestColumn))-1; // e.g. 5
            //Asociamos data con Indices de BBDD
            $indices = array('ID_CAMPANA','RUT','COD_SEDE','COD_CARRERA','JORNADA','OBSERVACION','ID_TIPO','ESTADO');            
            $html="";                        
            //Recorremos excel                             
            for ($row = 3; $row <= $highestRow; ++$row) {                
                 
                 for ($col = 0; $col <= $highestColumnIndex; ++$col) {         
                    
                   $lista[$row][$col] = $objWorksheet->getCellByColumnAndRow($col,$row)->getValue();                                                                     
                }
                    //Agregamos indices a array 
                    error_reporting(E_ERROR);                   
                    $data = array_combine($indices,$lista[$row]);
                            if (!$data){                        
                                $result = new JsonModel(array('status'=>'error','descr'=>"El archivo no corresponde a Maestro de Oportunidades"));
                                $result->setTerminal(true);
                                return $result;
                            }
                    //Replicamos para tabla feedback
                    $data['ESTADO_GRABADO'] = $data['ESTADO'];                                                     
                    //Agregamos estado y usuario al array                    
                    $data['USERNAME'] = $sid->offsetGet('usuario');                                         
                    //Insertamos nueva oportunidad en tabla OPORTUNIDADES                               
                    $data['ID_OPORTUNIDAD'] = $oportunidad->nuevaOportunidad($data);                                            
                    //Insertamos feedback de oportuinidad en tabla FEEDBACKS            
                    $feedback->nuevoFeedback($data);   
                    //Agregamos fila para mostrar en la vista
                    $html.="<tr>";
                    $html.="<td>".$data['ID_CAMPANA']."</td><td>".$data['RUT']
                         ."</td><td>".$data['COD_SEDE']."</td><td>".$data['COD_CARRERA']."</td><td>".$data['JORNADA']
                         ."</td><td>".$data['OBSERVACION']."</td><td>".$data['ID_TIPO']
                         ."</td><td>".$data['ESTADO'];
                    $html.="</tr>";                              
            }  
        //Retornamos a la vista 
        $descr = "Se han ingresado <strong>".($highestRow-2)."</strong> oportunidades exitosamente";   
        $this->layout('layout/admincentral');
        $result = new JsonModel(array('status'=>'ok','html'=>$html,'descr'=>$descr));
        return $result;
        
        
    }    
}
