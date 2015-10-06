<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2014 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Sistema\Model\Entity\Crm\UsuarioTable;
use Sistema\Model\Entity\Crm\UsuarioPerfilTable;
use Sistema\Model\Entity\Crm\PerfilTable;




use Zend\Session\Container;
use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;


class LoginController extends AbstractActionController
{
    public $dbAdapter;
    
    public function indexAction()
    {
        
        $sid = new Container('base');
        //$session->getManager()->getStorage()->clear();
       /* if ($sid->offsetExists('usuario')){
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application');
        }*/
        

        
        $id = (int) $this->params()->fromRoute('id', 0);
        $view = new ViewModel();
        if($id==1){
            $mensaje="El usuario ingresado no se encuentra registrado en el sistema o la contrase&ntilde;a es incorrecta";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==2){
            $mensaje="El usuario no tiene perfil configurado";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==3){
            $mensaje="Perfil de usuario no existe en el sistema";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==4){
            $mensaje="Finaliz&oacute; la sesi&oacute;n correctamente";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==6){
            $mensaje="Error en el cambio de clave, favor intente nuevamente o contacte nuestro soporte Telefónico";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }                 
            
            $this->layout('layout/login');
            return $view;
        
        
        
    }
    
   /* public function sendAction()
    {
        $data = $this->getRequest()->getPost();
        
       
        
        if($data['usuario']!=null && $data['password']!=null){
            
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $usuario = new UsuarioTable($this->dbAdapter);
            $listaUsuario = $usuario->getUsuario($data['usuario'],strrev($data['password']));
            if(!empty($listaUsuario)){
                
                
                
                if($listaUsuario[0]['activo']=='1'){
                    
                    $nroSession =(int)$listaUsuario[0]['nro_session'];
                    $tsession = new SessionTable($this->dbAdapter);
                    $nroSessionDB = count($tsession->obtenetSesion($listaUsuario[0]['id']));
                    
                    //logica de numero de session
                    if ($nroSession>0 && $nroSessionDB >= $nroSession){

                       return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>4));
                       //return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/login/index/4');
                        
                    } else {
                        //cantidad de modulos que posee el usuario                    
                        $modulo = $usuario->getModulo($this->dbAdapter,$listaUsuario[0]['id_perfil']);
                        
                        //iniciar session, creamos el contenedor de session de nombre base
                            $sid = new Container('base');
                            //con el offsetSet almacenamos la variable session
                            $sid->offsetSet('modulo', $modulo);
                            $sid->offsetSet('usuario', $listaUsuario[0]['usuario']);
                            $sid->offsetSet('id_usuario', $listaUsuario[0]['id']);
                            $valores = array('id_usuario'=>$listaUsuario[0]['id'],'ip_cliente'=>$_SERVER['REMOTE_ADDR']);
                            $sid->offsetSet('idSession',$tsession->crearSesion($valores));
                            
                            
                             if(count($modulo) > 1){
                                $urlHome = 'application';
                             }else{
                                $urlHome = $modulo[0]['url'];
                             } 
                             $sid->offsetSet('urlHome',$urlHome);
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/'.$urlHome); 
                    }
                     
                    
                    
                    
                    
                                        
                }else{
                    return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>2));
                    //return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/login/index/2');
                }
                
            }else{
                return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
               //return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/login/index/1'); 
            }
            
            
        }else{
            return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
            //return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application/login/index/1');
        }
        
        
        
        
        
        //return new ViewModel(array('data'=>$idSession));
    }*/
    
    public function sendAction()
    {                
                        $sid = new Container('base');
                        $sid->offsetSet('usuario','operadorA');
                        $sid->offsetSet('perfil', "operador");
                        $sid->offsetSet('desc_perfil', 'Operador de sistema'); 
                        $sid->offsetSet('logueado', 'si'); 
      /*  $data = $this->getRequest()->getPost();
        
        if($data['usuario']!=null && $data['password2']!=null){
            //Conectamos a BBDD
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            //Consultamos Usuario y Password en tabla USUARIOS
            $usuario = new UsuarioTable($this->dbAdapter);
            $listaUsuario = $usuario->getUsuario($data['usuario'],strrev($data['password2']));
            //Validamos existencia
            if(!empty($listaUsuario)){     
                    //Consultamos perfil segun usuario
                    $usuperfil = new UsuarioPerfilTable($this->dbAdapter);
                    $listausuperfil = $usuperfil->getPerfil($listaUsuario[0]['USERNAME']);
                    //Validamos existencia de perfil para usuario
                    if(!empty($listausuperfil[0]['ID_PERFIL'])){                                                
                        //Consultamos perfil segun usuario en tabla USUARIO_PERFIL
                        $perfil = new PerfilTable($this->dbAdapter);
                        $listaperfil = $perfil->getPerfil($listausuperfil[0]['ID_PERFIL']);
                        //Validamos existencia de perfil en tabla PERFIL                        
                        if(!empty($listaperfil[0]['ID_PERFIL'])){ 
                                                                                                                                                                                                
                        //return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>4));                                                                                                                                  
                        //Iniciamos la session
                        $sid = new Container('base');
                        $sid->offsetSet('usuario', $listaUsuario[0]['USERNAME']);
                        $sid->offsetSet('perfil', $listaperfil[0]['ID_PERFIL']);
                        $sid->offsetSet('desc_perfil', $listaperfil[0]['DESC_PERFIL']); 
                        $sid->offsetSet('logueado', 'si');                                                                                                                       
                                                
                             if($listaperfil[0]['ID_PERFIL'] == "operador"){
                                $urlHome = 'operador';
                             }
                             if($listaperfil[0]['ID_PERFIL'] == "adminsede"){
                                $urlHome = "adminsede";
                             }
                             if($listaperfil[0]['ID_PERFIL'] == "admcentral"){
                                $urlHome = "admcentral";
                             }                             
                             $urlHome = 'operador';
                             $sid->offsetSet('urlHome',$urlHome);
                             
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/'.$urlHome); 
                    }else{
                        return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>3));
                    }                 
                }else{
                    return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>2));
                }                                                                                                    
            }else{
                return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
            }                        
       }else{
            return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
        }
         //array('data'=>$idSession)    */                           
        return new ViewModel();
    }    
    /*
        public function sendAction()
    {                
        $data = $this->getRequest()->getPost();
        
        if($data['usuario']!=null && $data['password2']!=null){
            //Conectamos a BBDD
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            //Consultamos Usuario y Password en tabla USUARIOS
            $usuario = new UsuarioTable($this->dbAdapter);
         //   $listaUsuario = $usuario->getUsuario($data['usuario'],strrev($data['password2']));
            //Validamos existencia
            $listaUsuario = array('0'=>array('USERNAME'=>'operador'));
            if(!empty($listaUsuario)){     
                    //Consultamos perfil segun usuario
                    $usuperfil = new UsuarioPerfilTable($this->dbAdapter);
                  //  $listausuperfil = $usuperfil->getPerfil($listaUsuario[0]['USERNAME']);
                    //Validamos existencia de perfil para usuario
                    $listausuperfil[0]['ID_PERFIL'] ="s";
                    if(!empty($listausuperfil[0]['ID_PERFIL'])){                                                
                        //Consultamos perfil segun usuario en tabla USUARIO_PERFIL
                        $perfil = new PerfilTable($this->dbAdapter);
                       // $listaperfil = $perfil->getPerfil($listausuperfil[0]['ID_PERFIL']);
                        //Validamos existencia de perfil en tabla PERFIL
                         $listaperfil[0]['ID_PERFIL'] = "operador";
                        if(!empty($listaperfil[0]['ID_PERFIL'])){ 
                                                                                                                                                                                                
                        //return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>4));                                                                                    
                        
                        $listaUsuario[0]['USERNAME'] = "operador";
                        $listaperfil[0]['DESC_PERFIL'] = "Operador";
                       
                        
                        //Iniciamos la session
                        $sid = new Container('base');
                        $sid->offsetSet('usuario', $listaUsuario[0]['USERNAME']);
                        $sid->offsetSet('perfil', $listaperfil[0]['ID_PERFIL']);
                        $sid->offsetSet('desc_perfil', $listaperfil[0]['DESC_PERFIL']); 
                        $sid->offsetSet('logueado', 'si');                                                                                                                       
                                                
                             if($listaperfil[0]['ID_PERFIL'] == "operador"){
                                $urlHome = 'operador';
                             }
                             if($listaperfil[0]['ID_PERFIL'] == "adminsede"){
                                $urlHome = "adminsede";
                             }
                             if($listaperfil[0]['ID_PERFIL'] == "admcentral"){
                                $urlHome = "admcentral";
                             }                             
                             $urlHome = 'operador';
                             $sid->offsetSet('urlHome',$urlHome);
                             
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/'.$urlHome); 
                    }else{
                        return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>3));
                    }                 
                }else{
                    return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>2));
                }                                                                                                    
            }else{
                return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
            }                        
       }else{
            return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
        }
         //array('data'=>$idSession)                               
        return new ViewModel();
    }    
    
    */
    /* public function cambiaclaveAction($data)
    {
        
        $sid = new Container('base');
        //$session->getManager()->getStorage()->clear();
        if ($sid->offsetExists('usuario')){
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application');
        }        
        
       if($data['antigua']!=null && $data['nuevapassword']!=null && $data['nuevapassword2'])
       //Eliminamos la session registrado, para liberar licencia
       
        
       
       $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
       $tsession = new SessionTable($this->dbAdapter);
       $tsession->eliminarSesion($idSession);
       
       //destruimos todas las sessiones
       $sid->getManager()->getStorage()->clear();
       
       //eliminamos Cookies
        if(isset($_COOKIE['usuario']))
        {
            unset($_COOKIE['usuario']);
            //setcookie('usuario', '', time() - 3600); // empty value and old timestamp
        }
         if(isset($_COOKIE['password']))
        {
            unset($_COOKIE['password']);
            //setcookie('password', '', time() - 3600); // empty value and old timestamp
        }
        
        return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>3));
        
       
    }*/

    public function emailclaveAction()
    {
        $mail = $_POST['mail'];
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        $usu = new UsuarioTable($this->dbAdapter);
        $existe = $usu->getDatosMail($mail);        
        if (count($existe)>0)
        {
            $str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
            $cad = "";
                for($i=0;$i<8;$i++) {
                    $cad .= substr($str,rand(0,62),1);
                }
             $token = md5($cad);
            //$nuevaclave = substr($nuevaclave,3,11);                
         
             $nuevotoken = array('token'=>$token,'mail'=>$mail);
             $tok = new TokenTable($this->dbAdapter);                         
             $tok->nuevoToken($nuevotoken);
             $localhost = "";             
             $url = $localhost."/crm/public/application/recuperar/index/".$token;                                          
             $htmlMarkup = " html de correo recuperar contraseña"  ;                                  
             
          /*   $text = new MimePart($textContent);
             $text->type = "text/plain";*/

             $html = new MimePart($htmlMarkup);
             $html->type = "text/html";

            /* $image = new MimePart(fopen($pathToImage, 'r'));
             $image->type = "image/jpeg";*/

             $body = new MimeMessage();
             $body->setParts(array($html));
        
             $message = new Message();
             $message->addTo($mail)
             ->addFrom('noreply@crmadmision.cl', 'CRM-Admision')
             ->setSubject('Recuperar Contraseña')
             ->setBody($body);

             $transport = new SendmailTransport();
             $transport->send($message);                             
             $descripcion = "Se ha enviado un correo a :  ".$mail;                          
             $result = new JsonModel(array('descripcion'=>$descripcion,'status'=>'ok'));                                
             return $result;  
        }else{
          $descripcion="Lo siento, el correo no existe en nuestros registros...";    
          $result = new JsonModel(array('descripcion'=>$descripcion));                                
          return $result;
        }    
}        
    public function salirAction()
    {
       //Instancia de sesion
       $sid = new Container('base');
       
       //destruimos todas las sessiones
       $sid->getManager()->getStorage()->clear();
       
       //eliminamos Cookies
        if(isset($_COOKIE['usuario']))
        {
            unset($_COOKIE['usuario']);
            //setcookie('usuario', '', time() - 3600); // empty value and old timestamp
        }
         if(isset($_COOKIE['password']))
        {
            unset($_COOKIE['password']);
            //setcookie('password', '', time() - 3600); // empty value and old timestamp
        }
        
        return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>4));
        
       
    }                           
}
