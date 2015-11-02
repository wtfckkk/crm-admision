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
use Sistema\Model\Entity\Crm\SedeCampanaTable;
use Sistema\Model\Entity\Crm\CampanaTable;
use Sistema\Model\Entity\Crm\CarrerasTable;
use Sistema\Model\Entity\Crm\SedeCarreraTable;
use Sistema\Model\Entity\Crm\OportunidadTable;
use Sistema\Model\Entity\Crm\TipoFeedbackTable;
use Sistema\Model\Entity\Crm\FeedbackTable;
use Sistema\Model\Entity\Crm\UsuarioTable;
use Sistema\Model\Entity\Crm\UsuarioSedeTable;
use Sistema\Model\Entity\Crm\UsuarioPerfilTable;
use Sistema\Model\Entity\Crm\TipoCampanaTable;
use Sistema\Model\Entity\Crm\SedeTable;


class AdministrarController extends AbstractActionController
{
    public function indexAction()
    {
        
        $this->layout('layout/admincentral');
        return new ViewModel();
        
        
    }
    
        public function campanasAction()
    {
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas
        $campana = new CampanaTable($this->dbAdapter); 
        $usuperf = new UsuarioPerfilTable($this->dbAdapter);
        $ususede = new UsuarioSedeTable($this->dbAdapter); 
        //Consultamos campañas del sistema 
            $campanas = $campana->getCampanas($this->dbAdapter);
            $campanas = str_replace("ñ","&ntilde;",$campanas);
            $com = "'";
            //Cargamos data para grilla             
            $html = '';
                for ($i=0;$i<count($campanas);$i++){
                    if($campanas[$i]['ACTIVO']=="s"){$activo="Si";}else{$activo="No";}                    
                    $html .= '<tr>';
                    $html = $html.'<td>'.$campanas[$i]['NOMBRE_CAMPANA'].'</td>
                                 <td>'.$campanas[$i]['DESC_TIPO'].'</td>
                                 <td>'.$activo.'</td>
                                 <td>'.$campanas[$i]['NOMBRE_SEDE'].'</td>
                                 <td><a onclick="borraCampana('.$com.$campanas[$i]['ID_CAMPANA'].$com.','.$com.$campanas[$i]['COD_SEDE'].$com.')" class="btn red-haze" ><i class="fa fa-trash"></i></a> 
                                             &nbsp; <a onclick="editc('.$campanas[$i]['ID_CAMPANA'].')"  class="btn blue" ><i class="fa fa-edit"></i></a></td>';
                                             $html .= '</tr>';                                                                                                                                                                                                             
                        }                                         
            
            //Retornamos a la vista                
            $this->layout('layout/admincentral');
            $result = new ViewModel(array('html'=>$html));             
            return $result; 
        
        
    }
    
        public function nuevacampanaAction()
    {
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas
        $tipocam = new TipoCampanaTable($this->dbAdapter); 
        $sede    = new SedeTable($this->dbAdapter);
        $campana = new CampanaTable($this->dbAdapter); 
        $sedecam = new SedeCampanaTable($this->dbAdapter); 
        
        //Perfil
        $pefil = "operador";
        //Obtenemos datos de sesion           
        $sid = new Container('base');
        $cod_sede = $sid->offsetGet('cod_sede');
        //Validamos
        if(isset($lista['NOMBRE_CAMPANA']))
         {                                                                                                                         
            //Obtenemos datos de sesion           
            $sid = new Container('base');
            $lista['USERNAME'] = $sid->offsetGet('usuario');
            $lista['ANO_ACADEMICO'] = date('Y');
            //Insertamos nueva campaña           
            $id_campana = $campana->nuevaCampana($lista);
            //Asociamos campaña para cada sede
            for($i=0;$i<count($lista['COD_SEDE']);$i++)
            {
                $sedecam->nuevaSedeCampana($id_campana,$lista['COD_SEDE'][$i]);   
            }
            //Descripcion y flag true                        
            $flag = "true";
            $descr = "Campa&ntilde;a creada exitosamente";              
                                    
            //Retornamos a la vista
            $result = new JsonModel(array('flag'=>$flag,'descr'=>$descr));                     
            return $result;
            
         }else{
            //Consultamos tipo de campañas para combo
            $combocampana = $tipocam->getCombo();
            //Consultamos Sedes para checkboxs
            $sedes = $sede->getSedes();
            //Retornamos a la vista                
            $this->layout('layout/admincentral');
            $result = new ViewModel(array('combocampana'=>$combocampana,
                                          'sedes'=>$sedes ));
            $result->setTerminal(true); 
            return $result; 
        }
        
    }
    public function borracampanaAction()
    {
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas
        $campana = new CampanaTable($this->dbAdapter); 
        $sedecam = new SedeCampanaTable($this->dbAdapter);         
        //Borramos de cada tabla        
        $sedecam->borraCampana($lista['id_campana'],$lista['cod_sede']);
       // $campana->borraCampana($lista['id_campana']);                                       
        
        //Retornamos a la vista                
        $this->layout('layout/admincentral');
        $result = new JsonModel(array('status'=>'ok'));
        $result->setTerminal(true);
        return $result;
                        
    }
        public function editarcampanaAction()
    {   
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $campana = new CampanaTable($this->dbAdapter);      
        $tipocam = new TipoCampanaTable($this->dbAdapter); 
        $sede    = new SedeTable($this->dbAdapter);
        $sedecam = new SedeCampanaTable($this->dbAdapter); 
        if(isset($lista['NOMBRE_CAMPANA']))
        {
            
            //Obtenemos datos de sesion           
            $sid = new Container('base');
            $lista['USERNAME'] = $sid->offsetGet('usuario');
            $lista['ANO_ACADEMICO'] = date('Y');
            //Editamos campaña           
            $campana->editarCampana($lista['ID_CAMPANA'],$lista);
            //Validamos si se asociaron nuevas sedes
                if(isset($lista['COD_SEDE'])){
                    //Asociamos campaña para cada sede
                    for($i=0;$i<count($lista['COD_SEDE']);$i++)
                    {
                         $sedecam->nuevaSedeCampana($lista['ID_CAMPANA'],$lista['COD_SEDE'][$i]);   
                    }
                }                              
            $descr = "Edici&oacute;n de campa&ntilde;a exitosa";              
            $flag = "true";                    
            //Retornamos a la vista
            $result = new JsonModel(array('flag'=>$flag,'descr'=>$descr));                     
            return $result;                                                                                      
            
        }                      
        else{
        //Consultamos tipo de campañas para combo
        $combocampana = $tipocam->getCombo();   
        //Consultamos Sedes para checkboxs
        $sedes = $sede->getSedes();                           
        //Obtenemos datos de Campana
        $campanas = $campana->getCampanaxId($lista['ID']);
        //Obtenemos sedes asociadas a la campaña
        $sedecampanas = $sedecam->getSedexCampana($lista['ID']);           
        //Retornamos a la vista                            
        $result = new ViewModel(array('campana'=>$campanas,
                                      'combocampana'=>$combocampana,
                                      'sedes'=>$sedes,
                                      'sedecampanas'=>$sedecampanas,
                                ));
        $result->setTerminal(true); 
        return $result; 
        }
        
    }
    
    public function nuevotipoAction()
    {   
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $tipocam = new TipoCampanaTable($this->dbAdapter);        
        if($lista['NOMBRE_CAMPANA']!=null){
            //Insertamos nuevo tipo de Campaña
            $tipocam->nuevoTipo($lista['NOMBRE_CAMPANA']); 
            //Retornamos a la vista    
            $descr = "Se ha ingresado nuevo tipo de campa&ntilde;a correctamente";                         
            $result = new ViewModel(array('status'=>'ok','tipos'=>$tipos));
            $result->setTerminal(true); 
            return $result;            
        }else{
            //Obtenemos tipos de campañas   
            $tipos =  $tipocam->getTipos();                        
            //Retornamos a la vista                            
            $result = new ViewModel(array('status'=>'ok','tipos'=>$tipos));
            $result->setTerminal(true); 
            return $result; 
         
         }                
    }
    
   
}
