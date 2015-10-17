<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Adminsede\Controller;

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


class AdministrarController extends AbstractActionController
{
    public function indexAction()
    {
        
        $this->layout('layout/adminsede');
        return new ViewModel();
        
        
    }
    
        public function operadoresAction()
    {
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas
        $usuario = new UsuarioTable($this->dbAdapter); 
        $usuperf = new UsuarioPerfilTable($this->dbAdapter);
        $ususede = new UsuarioSedeTable($this->dbAdapter); 
        //Perfil
        $pefil = "operador";
        //Obtenemos datos de sesion           
        $sid = new Container('base');
        $cod_sede = $sid->offsetGet('cod_sede');
        //Consultamos operarios del sistema por sede
            $usuarios = $ususede->getUsuariosxSede($this->dbAdapter,$pefil,$cod_sede);
            $com = "'";
            //Cargamos data para grilla             
            $html = '<tr>';
                for ($i=0;$i<count($usuarios);$i++){                         
                  $html = $html.'<td>'.$usuarios[$i]['NOMBRE_FULL'].'</td>
                                 <td>'.$usuarios[$i]['USERNAME'].'</td>
                                 <td>'.$usuarios[$i]['ID_PERFIL'].'</td>
                                 <td>'.$usuarios[$i]['NOMBRE_SEDE'].'</td>
                                 <td><a onclick="borraUsuario('.$com.$usuarios[$i]['USERNAME'].$com.')" class="btn red-haze" ><i class="fa fa-trash"></i></a> 
                                             &nbsp; <a onclick="editUsuario('.$com.$usuarios[$i]['USERNAME'].$com.')"  class="btn blue" ><i class="fa fa-edit"></i></a></td>'; 
                                 
                                   
                                 $html .= '</tr>';                                                                                                        
                        }                                         
        
            //Retornamos a la vista                
            $this->layout('layout/adminsede');
            $result = new ViewModel(array('html'=>$html));             
            return $result; 
        
        
    }
    
        public function nuevoperAction()
    {
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas
        $usuario = new UsuarioTable($this->dbAdapter); 
        $usuperf = new UsuarioPerfilTable($this->dbAdapter);
        $ususede = new UsuarioSedeTable($this->dbAdapter); 
        //Perfil
        $pefil = "operador";
        //Obtenemos datos de sesion           
        $sid = new Container('base');
        $cod_sede = $sid->offsetGet('cod_sede');
        //Validamos
        if(isset($lista['PASSWORD']))
         {                                                
            //Consultamos y validamos si existe usuario             
            $existe = $usuario->getUser($lista['USERNAME']);
                if(count($existe)>0)
                {
                    $descr = "Usuario ya existe en el sistema";
                    $flag = "false";
                    //Retornamos a la vista
                    $result = new JsonModel(array('flag'=>$flag,'descr'=>$descr));                     
                    return $result; 
                }
            //Ciframos password
            $lista['PASSWORD'] = md5($lista['PASSWORD']);              
            
            //Insertamos nuevo usuario en tablas de usuario
            $usuario->nuevoUsuario($lista); 
            $usuperf->nuevoUsuPerfil($lista['USERNAME'],$pefil);
            $ususede->nuevoUsuSede($lista['USERNAME'],$cod_sede);            
            $flag = "true";
            $descr = "Usuario creado exitosamente";              
                                    
            //Retornamos a la vista
            $result = new JsonModel(array('flag'=>$flag,'descr'=>$descr));                     
            return $result;
            
         }else{
            
            //Retornamos a la vista                
            $this->layout('layout/adminsede');
            $result = new ViewModel();
            $result->setTerminal(true); 
            return $result; 
        }
        
    }
    public function borrausuarioAction()
    {
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas
        $usuario = new UsuarioTable($this->dbAdapter); 
        $usuperf = new UsuarioPerfilTable($this->dbAdapter);
        $ususede = new UsuarioSedeTable($this->dbAdapter);
        //Borramos de cada tabla
        $usuperf->borraUsuario($lista['user']);
        $ususede->borraUsuario($lista['user']);
        $usuario->borraUsuario($lista['user']);                
        
        //Retornamos a la vista                
        $this->layout('layout/adminsede');
        $result = new JsonModel(array('status'=>'ok'));
        $result->setTerminal(true);
        return $result;
                        
    }
        public function editaroperAction()
    {   
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $ususede = new UsuarioSedeTable($this->dbAdapter);    
        //Perfil
        $pefil = "operador";        
        //Obtenemos datos de sesion           
        $sid = new Container('base');
        $cod_sede = $sid->offsetGet('cod_sede');  
        //Obtenemos datos del usuario
        $usuarios = $ususede->getUsuarioxSede($this->dbAdapter,$lista['user'],$pefil,$cod_sede);           
        //Retornamos a la vista                            
        $result = new ViewModel(array('usuarios'=>$usuarios));
        $result->setTerminal(true); 
        return $result; 
        
        
    }
    
    public function editapassAction()
    {   
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter');
        //Tablas
        $usuario = new UsuarioTable($this->dbAdapter);                              
        //Editamos contraseña de Usuario
        $usuario->editarUsuario($lista['user'],md5($lista['pass']));           
        //Retornamos a la vista                            
        $result = new JsonModel(array('status'=>'ok'));         
        return $result; 
        
        
    }
    
   
}
