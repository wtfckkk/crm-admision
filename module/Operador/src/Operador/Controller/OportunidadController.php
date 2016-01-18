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


class OportunidadController extends AbstractActionController
{
    public function indexAction()
    {
        
        $this->layout('layout/operador');
        return new ViewModel();
        
        
    }
    // ----------------------------------------INGRESAR OPORTUNIDAD
    
    public function nuevaAction()
    {
        
         $this->layout('layout/operador');    
        return new ViewModel();
        
        
    }
    
    public function buscarutAction()
    {                                             
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Quitamos formato RUT
        $lista['RUT'] = explode("-",$lista['RUT']);
        $lista['DV']  = $lista['rut'][1];
        $lista['RUT'] = str_replace(".","",$lista['RUT'][0]); 
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
	               $detalletable =  $pdetalle->getDetallexID($cab_detalle[$i]['ID_DETALLE']);  
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
                        
            $detalle = $pdetalle->getDetallexID($cab_detalle[0]['ID_DETALLE']);
            
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
        $lista['RUT'] = explode("-",$lista['RUT']);
        $lista['DV']  = $lista['RUT'][1];
        $lista['RUT'] = str_replace(".","",$lista['RUT'][0]); 
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
            
           //Tablas
           $pcabecera    = new ProspectoCabeceraTable($this->dbAdapter);
           $pdetalle     = new ProspectoDetalleTable($this->dbAdapter);
           $pcabedetalle = new ProsCabeceraDetalleTable($this->dbAdapter);           
           //Calculamos DV
//DV lo ingresa usuario // $lista['DV'] =  \Utils::calculaDV($lista['RUT']); 
            //Validamos si existe prospecto       
            $existe = $pcabecera->getDatoxRut($lista['RUT']);            
            if(count($existe)>0)
               {  
                //Actualizamos datos de prospecto
                $pcabecera->editarProsCabecera($lista,$lista['RUT']);
                
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
                }
            //Buscamos detalle de Prospecto            
            $cab_detalle   = $pcabedetalle->getIdDetalle($lista['RUT']);
            //Cargamos data para grilla
            $html = "";
                for ($i=0;$i<count($cab_detalle);$i++){
	               $detalletable =  $pdetalle->getDetallexID($cab_detalle[$i]['ID_DETALLE']);  
                        for ($j=0;$j<count($detalletable);$j++){
                            $html .= '<tr>';
                            foreach ($detalletable[$j] as $key => $valor) {
                                $html = $html.'<td>'.$valor.'</td>';                                   
                                } 
                            $html .= '</tr>';       
                        }
                }
                $html .= "</tr>";
                                
                        
            //Retornamos a la Vista            
            $result = new JsonModel(array('status'=>'ok','descripcion'=>$desc,'tabla'=>$html));
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
           $campana   = new CampanaTable($this->dbAdapter);
           $sedecarr  = new SedeCarreraTable($this->dbAdapter);
           $carrera   = new CarrerasTable($this->dbAdapter);
           
           
           
           
           //Consultamos codigo de sedes por usuario       
           $codesede = $usersede->getSede($lista['user']);
           //Consultamos Carreras por sede para combo
           $idcarreras = $sedecarr->getIDCarrera($codesede[0]['COD_SEDE']);            
           $in ="'".implode("','", $idcarreras)."'";
           $combocarr = $carrera->getCombo($this->dbAdapter,$in);
           //Consultamos si existen campañas
           $campanas = $campana->getCampanas($this->dbAdapter);
           //Respondemos false si no hay campanas asociadas a la sede
              if (count($campanas)<1){
                //Retornamos a la Vista            
                $result = new JsonModel(array('status'=>'nok','carreras'=>$combocarr,'descr'=>'No existen campa&ntilde;as creadas en el sistema')); 
                $result->setTerminal(true);            
                return $result; 
              } 
           //Consultamos Campañas por usuario para combo
           $idcampanas = $sedecamp->getIDCampana($codesede[0]['COD_SEDE']);                     
           $combocamp = $campana->getCombo($this->dbAdapter,implode(',',$idcampanas));           
           
           
          // $test= implode(',',$idcampanas);
           
           
        
            
            //Retornamos a la Vista            
            $result = new JsonModel(array('status'=>'ok','campanas'=>$combocamp,'carreras'=>$combocarr));
            $result->setTerminal(true); 
            return $result; 
        
    }
    
    public function buscajornadaAction()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');            
        //Tabla
        $sedecarr  = new SedeCarreraTable($this->dbAdapter);
        //Consultamos jornadas por Carrera
        $jornada = $sedecarr->getJornada($lista['cod_carrera'],$lista['sede']);
                                    
        //Retornamos a la Vista            
            $result = new JsonModel(array('status'=>'ok','jornada'=>$jornada));
            $result->setTerminal(true); 
            return $result;
        
        
    }
    public function actualizarestadoAction()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        $lista['RUT'] = explode("-",$lista['RUT']);
        $lista['DV']  = $lista['rut'][1];
        $lista['RUT'] = str_replace(".","",$lista['RUT'][0]); 
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');            
        //Tablas
        $pcabecera = new ProspectoCabeceraTable($this->dbAdapter);
        //Consultamos jornadas por Carrera
        $pcabecera->editarEstado($lista['estado'],$lista['rut']);
                                    
        //Retornamos a la Vista            
            $result = new JsonModel(array('estado'=>$lista['estado']));
            $result->setTerminal(true); 
            return $result;
        
        
    }
    
    public function nuevaoportunidadAction()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        $lista['RUT'] = explode("-",$lista['RUT']);
        $lista['DV']  = $lista['rut'][1];
        $lista['RUT'] = str_replace(".","",$lista['RUT'][0]); 
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');            
        //Obtenemos datos de sesion        
        $sid = new Container('base');
        $lista['COD_SEDE'] = $sid->offsetGet('cod_sede');
        $lista['USERNAME'] = $sid->offsetGet('usuario');
              //Respondemos false si no hay campanas seleccionadas
              if ($lista['ID_CAMPANA'] == null){
                //Retornamos a la Vista            
                $result = new JsonModel(array('status'=>'nok','descr'=>'No se puede ingresar oportunidad sin seleccionar campa&ntilde;a')); 
                $result->setTerminal(true);            
                return $result; 
              } 
        //Ingresamos nueva oportunidad y la consultamos  
        $oportunidad = new OportunidadTable($this->dbAdapter);  
        $id_oportunidad = $oportunidad->nuevaOportunidad($lista);
        $nueva_oportunidad = $oportunidad->getOportunidad($id_oportunidad);             
                                        
        //Retornamos a la Vista
        $descr = "Se ha ingresado nueva oportunidad para rut ".$lista['RUT'];            
        $result = new JsonModel(array('status'=>'ok','descr'=>$descr));
        $result->setTerminal(true); 
        return $result;
        
        
    }
    
    // ----------------------------------------SEGUIMIENTO POR RUT
    
    public function rutAction()
    {        
        $this->layout('layout/operador');    
        return new ViewModel();                
    }
    
    
    public function existerutAction()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Quitamos formato RUT
        $lista['RUT'] = explode("-",$lista['RUT']);
        $lista['DV']  = $lista['rut'][1];
        $lista['RUT'] = str_replace(".","",$lista['RUT'][0]); 
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $pcabecera   = new ProspectoCabeceraTable($this->dbAdapter);       
        $oportunidad = new OportunidadTable($this->dbAdapter);  
        //Obtenemos datos de sesion        
        $sid = new Container('base');
        $lista['COD_SEDE'] = $sid->offsetGet('cod_sede');  
        //Validamos si existe prospecto       
        $existe = $pcabecera->getDatoxRut($lista['RUT']); 
          if(count($existe)>0){
            $opor_rut = $oportunidad->getOporRutSede($this->dbAdapter,$lista['RUT'],$lista['COD_SEDE']);
              if (count($opor_rut)>0){
                $descr = "Busqueda existosa!";
                $flag ="true";
              }else{
                $descr = "No existen oportunidades creadas para rut ".$lista['RUT']." en su sede. ";
                $flag ="false";              
              }    
          }
          else{
            $descr = "Rut ".$lista['RUT']." no existe en el sistema";
            $flag ="false";
          }
        
        //Retornamos a la Vista            
        $result = new JsonModel(array('flag'=>$flag,'descr'=>$descr));
        $result->setTerminal(true); 
        return $result;
        
        
    }
    
    public function rut2Action()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Quitamos formato RUT
        $lista['RUT'] = explode("-",$lista['RUT']);
        $lista['DV']  = $lista['rut'][1];
        $lista['RUT'] = str_replace(".","",$lista['RUT'][0]); 
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $pcabecera  = new ProspectoCabeceraTable($this->dbAdapter);
        $oportunid  = new OportunidadTable($this->dbAdapter);
        //Obtenemos datos de sesion        
        $sid = new Container('base');
        $lista['COD_SEDE'] = $sid->offsetGet('cod_sede');    
        //Consultamos datos de Prospecto         
        $prospecto = $pcabecera->getDatoxRut($lista['RUT']); 
        //OBtenemos oportunidades para prospecto
        $oportunidad = $oportunid->getOporRutSede($this->dbAdapter,$lista['RUT'],$lista['COD_SEDE']);
        //Retornamos a la vista
        $this->layout('layout/operador');  
        $result =  new ViewModel(array('prospecto'=>$prospecto,'oportunidad'=>$oportunidad));        
        return $result;
        
        
    }
    
    
    public function rut3Action()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Quitamos formato RUT
        $lista['RUT'] = explode("-",$lista['RUT']);
        $lista['DV']  = $lista['rut'][1];
        $lista['RUT'] = str_replace(".","",$lista['RUT'][0]);  
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $pcabecera    = new ProspectoCabeceraTable($this->dbAdapter);
        $pdetalle     = new ProspectoDetalleTable($this->dbAdapter);
        $oportunid    = new OportunidadTable($this->dbAdapter);
        $pcabedetalle = new ProsCabeceraDetalleTable($this->dbAdapter);
        $tipofeed     = new TipoFeedbackTable($this->dbAdapter);
        //Obtenemos datos de sesion        
        $sid = new Container('base');
        $lista['COD_SEDE'] = $sid->offsetGet('cod_sede');  
        //Consultamos datos de Prospecto         
        $prospecto = $pcabecera->getDatoxRut($lista['rut']); 
        //Consultamos detalle de Prospecto         
        $prdetalle   = $pdetalle->getDetallexRUT($this->dbAdapter,$lista['rut']);
        $cab_detalle = $pcabedetalle->getIdDetalle($lista['rut']);
                //Cargamos data para grilla
                $html = "";
                for ($i=0;$i<count($cab_detalle);$i++){
	               $detalletable =  $pdetalle->getDetallexID($cab_detalle[$i]['ID_DETALLE']);  
                        for ($j=0;$j<count($detalletable);$j++){
                            $html .= '<tr>';
                            foreach ($detalletable[$j] as $key => $valor) {
                                $html = $html.'<td>'.$valor.'</td>';                                   
                                } 
                            $html .= '</tr>';       
                        }
                }
                $html .= "</tr>";
        //Consultamos detalle de Oportunidad         
        $opor_detalle = $oportunid->getdetOportunidad($this->dbAdapter,$lista['id_opor']);
        //Consultamos tipoFeedback para combo
        $combofeedback = $tipofeed->getCombo();   
        $this->layout('layout/operador');    
        $result =  new ViewModel(array('prospecto'=>$prospecto,
                                       'prdetalle'=>$prdetalle,
                                       'opor_detalle'=>$opor_detalle,
                                       'html'=>$html,
                                       'combofeedback'=>$combofeedback));
        $result->setTerminal(true);
        return $result; ;
        
        
    }
    
    public function nuevofeedbackAction()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $feedback    = new FeedbackTable($this->dbAdapter);
        $oportunidad = new OportunidadTable($this->dbAdapter);
        //Obtenemos datos de sesion        
        $sid = new Container('base');
        $lista['USERNAME'] = $sid->offsetGet('usuario'); 
        //Actualizamos estado de oportunidad        
        $oportunidad->editarEstado($lista['ID_OPORTUNIDAD'],$lista['ESTADO_GRABADO']);
        //Ingresamos nuevo feedback       
        $feedback->nuevoFeedback($lista);
        
        
        //Retornamos a la vista
        $this->layout('layout/operador');  
        $result =  new JsonModel(array('status'=>'ok'));        
        return $result;
        
        
    }
    // ----------------------------------------SEGUIMIENTO POR CAMPÀÑA
    public function campanaAction()
    {
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $campana = new CampanaTable($this->dbAdapter);
        //Obtenemos datos de sesion        
        $sid = new Container('base');        
        //Consultamos campanas para sede 
        $combo_campana = $campana->getComboSede($this->dbAdapter,$sid->offsetGet('cod_sede'));
        
        //Consultamos tipoFeedback para combo   
        $this->layout('layout/operador');    
        $result =  new ViewModel(array('combo_campana'=>$combo_campana));        
        return $result; ;                
    }
    
     public function buscaopcampanaAction()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas        
        $oportunidad = new OportunidadTable($this->dbAdapter);
        //Obtenemos datos de sesion        
        $sid = new Container('base'); 
        $campanas = $oportunidad->getOporCampana($this->dbAdapter,$sid->offsetGet('cod_sede'),$lista['ID_CAMPANA']);        
          if(count($campanas)>0){          
            $descr = "Busqueda Exitosa!";
            $flag = "true";
          }
          else{
            $descr = "No existen oportunidades asociadas a esta campa&ntilde;a";
            $flag = "false";
          }         
        //Retornamos a la Vista            
        $result =  new JsonModel(array('flag'=>$flag,'descr'=>$descr));        
        return $result;
        
        
    }
    public function campana2Action()
    {
        //Obtenemos datos POST
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas        
        $oportunid    = new OportunidadTable($this->dbAdapter);
        $pcabecera    = new ProspectoCabeceraTable($this->dbAdapter);
        $pdetalle     = new ProspectoDetalleTable($this->dbAdapter);
        $oportunidad  = new OportunidadTable($this->dbAdapter);
        $pcabedetalle = new ProsCabeceraDetalleTable($this->dbAdapter);
        $tipofeed     = new TipoFeedbackTable($this->dbAdapter);
        //Obtenemos datos de sesion        
        $sid = new Container('base'); 
        //Buscamos oportunidad
        $campanas = $oportunidad->getOporCampana($this->dbAdapter,$sid->offsetGet('cod_sede'),$lista['ID_CAMPANA']);  
        $i = array_rand($campanas);
        //Consultamos datos de Prospecto         
        $prospecto = $pcabecera->getDatoxRut($campanas[$i]['RUT']);
        //Damos formato a RUT
        $dv = \Utils::calculaDV($prospecto[0]['RUT']);
        $prospecto['0']['RUT'] = number_format($prospecto['0']['RUT'],-3,"",".")."-".$dv;
        //Consultamos detalle de Prospecto         
        $prdetalle   = $pdetalle->getDetallexRUT($this->dbAdapter,$campanas[$i]['RUT']);
        //Consultamos detalle de Oportunidad         
        $opor_detalle = $oportunid->getdetOportunidad($this->dbAdapter,$campanas[$i]['ID_OPORTUNIDAD']);
        
        //Consultamos detalle de Prospecto                 
        $cab_detalle = $pcabedetalle->getIdDetalle($campanas[$i]['RUT']);
                //Cargamos data para grilla
                $html = "";
                for ($i=0;$i<count($cab_detalle);$i++){
	               $detalletable =  $pdetalle->getDetallexID($cab_detalle[$i]['ID_DETALLE']);  
                        for ($j=0;$j<count($detalletable);$j++){
                            $html .= '<tr>';
                            foreach ($detalletable[$j] as $key => $valor) {
                                $html = $html.'<td>'.$valor.'</td>';                                   
                                } 
                            $html .= '</tr>';       
                        }
                }
                $html .= "</tr>";
        
        
         //Consultamos tipo feedback
         $combofeedback = $tipofeed->getCombo();    
                
        //Retornamos a la vista
        $this->layout('layout/operador');               
        $result =  new ViewModel(array('prospecto'=>$prospecto,
                                        'prdetalle'=>$prdetalle,
                                        'opor_detalle'=>$opor_detalle,
                                        'html'=>$html,
                                        'combofeedback'=>$combofeedback));        
        return $result; 
        
        
    }
    
    public function agendamientosAction(){
        
        //Retornamos a la vista
        $this->layout('layout/operador');               
        $result =  new ViewModel();        
        return $result; 
    }
    
    public function excelagendamientosAction()
    {
      //Obtenemos datos POST
      $lista = $this->request->getPost();
      //Conectamos con BBDD
      $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');  
      //Instancias
      $opor = new OportunidadTable($this->dbAdapter);
      $sid = new Container('base');
      //Quitamos - a Fechas
      $fecha_inicio = str_replace("-","",$lista['fecha_inicial']);  
      $fecha_final = str_replace("-","",$lista['fecha_final']); 
      $array = $opor->getAgendamientos($this->dbAdapter,$fecha_inicio,$fecha_final,$sid->offsetGet('usuario'));
      
      //Creamos excel
        $objPHPExcel = new \PHPExcel();
        $campos = array_keys($array[0]);   

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("CRM La Araucana")
                             ->setTitle("Informe de Agendamientos")                             
                             ->setDescription("Agendamientos por fecha")
                             ->setCategory("");
                             
                //Cargamos Nombre de campos
            $objPHPExcel->getActiveSheet()->setCellValue('A1',$campos[0]);
            $objPHPExcel->getActiveSheet()->setCellValue('B1',$campos[1]);
            $objPHPExcel->getActiveSheet()->setCellValue('C1',$campos[2]);
            $objPHPExcel->getActiveSheet()->setCellValue('D1',$campos[3]);
            $objPHPExcel->getActiveSheet()->setCellValue('E1',$campos[4]);
            $objPHPExcel->getActiveSheet()->setCellValue('F1',$campos[5]);
            $objPHPExcel->getActiveSheet()->setCellValue('G1',$campos[6]);
            $objPHPExcel->getActiveSheet()->setCellValue('H1',$campos[7]);
            $objPHPExcel->getActiveSheet()->setCellValue('I1',$campos[8]);
            $objPHPExcel->getActiveSheet()->setCellValue('J1',$campos[9]);
            $objPHPExcel->getActiveSheet()->setCellValue('K1',$campos[10]);
            $objPHPExcel->getActiveSheet()->setCellValue('L1',$campos[11]);            
            $objPHPExcel->getActiveSheet()->setCellValue('M1',$campos[12]);
            $objPHPExcel->getActiveSheet()->setCellValue('N1',$campos[13]);
            $objPHPExcel->getActiveSheet()->setCellValue('O1',$campos[14]);
            $objPHPExcel->getActiveSheet()->setCellValue('P1',$campos[15]);
            $objPHPExcel->getActiveSheet()->setCellValue('Q1',$campos[16]);
            $objPHPExcel->getActiveSheet()->setCellValue('R1',$campos[17]);
            $objPHPExcel->getActiveSheet()->setCellValue('S1',$campos[18]);                     
            //Alineamos y coloreamos excel
            $ews = $objPHPExcel->getSheet(0);
            $header = 'a1:s1';
            $ews->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
            $style = array(
               'font' => array('bold' => true,),
               'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
               );
            $ews->getStyle($header)->applyFromArray($style);
            for ($col = ord('a'); $col <= ord('s'); $col++)
            {
                $ews->getColumnDimension(chr($col))->setAutoSize(true);
            }

            // Agregamos array al excel
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->fromArray($array, null, 'A2');
            
            //Grabamos Excel
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $filename='/var/www/html/crm/excel/agendamientos/Agendamiento_'.$sid->offsetGet('usuario').'_'.date('YmdGis').'.xlsx';
            $objWriter->save($filename);   
      

      //Retornamos a la Vista            
      $desc = "test";
      $result =  new JsonModel(array('status'=>'ok','ruta'=>str_replace("/var/www/html","",$filename),'desc'=>"Planilla excel lista para descarga"));        
      return $result;
      
      
        
    }
}
