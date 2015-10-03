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
use Sistema\Model\Entity\General\UsuarioTable;
use Sistema\Model\Entity\General\PersonaTable;
use Sistema\Model\Entity\General\SessionTable;
use Sistema\Model\Entity\General\TokenTable;
use Sistema\Model\Entity\General\DbTable;

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
        if ($sid->offsetExists('usuario')){
            return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/application');
        }
        

        
        $id = (int) $this->params()->fromRoute('id', 0);
        $view = new ViewModel();
        if($id==1){
            $mensaje="El usuario ingresado no se encuentra registrado en el sistema o la contraseña es incorrecta";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==2){
            $mensaje="El usuario se encuentra desactivado";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==3){
            $mensaje="Finalizó la sesión correctamente";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==4){
            $mensaje="El usuario ya se encuentra en sesión en otro dispositivo, se alcanzó el limite permitido de sesión";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==5){
            $mensaje="El usuario esta habilitado, pero no esta asociado algun condominio";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        } 
        if($id==6){
            $mensaje="Error en el cambio de clave, favor intente nuevamente o contacte nuestro soporte Telefónico";
            $view = new ViewModel(array('mensaje'=>$mensaje));
        }
        if($id==7){
            $mensaje="Permiso Denegado. Perfil no autorizado";
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
        $data = $this->getRequest()->getPost();
        
        if($data['usuariobecheck']!=null && $data['password2']!=null){
            
            $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
            $usuario = new UsuarioTable($this->dbAdapter);
            $listaUsuario = $usuario->getUsuario($data['usuariobecheck'],strrev($data['password2']));
            if(!empty($listaUsuario)){
                
                if($listaUsuario[0]['activo']=='1'){
                    
                    //Obtener la lista de edificios que pertenece
                    $dbTable = new DbTable($this->dbAdapter);
                    $listDb = $dbTable->listDBUser($this->dbAdapter,$listaUsuario[0]['id']);
                    
                    if(empty($listDb)){
                      return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>5));  
                    }
                    
                    //Verificamos la licencia del usuario
                    $nroSession = (int)$listDb[0]['nro_session'];
                    $tsession = new SessionTable($this->dbAdapter);
                    $nroSessionDB = count($tsession->obtenetSesion($listaUsuario[0]['id'],$listDb[0]['id']));
                    
                    if ($nroSession>0 && $nroSessionDB >= $nroSession){
                      return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>4));
                    }else{
                        //Iniciamos la session
                        $sid = new Container('base');
                        $sid->offsetSet('usuario', $listaUsuario[0]['usuario']);
                        $sid->offsetSet('id_usuario', $listaUsuario[0]['id']);
                        $sid->offsetSet('id_db',$listDb[0]['id']); 
                         
                        
                        
                        //Usuario posee mas edificios
                        if (count($listDb)>1){
                           $sid->offsetSet('dbParam', $listDb);
                           return $this->forward()->dispatch('Application\Controller\Index',array('action'=>'db')); 
                         }else{
                            $sid->offsetSet('nombreComercial', $listDb[0]['nombre']);
                            $sid->offsetSet('id_db', $listDb[0]['id']); 
                            $sid->offsetSet('perfil',$listDb[0]['perfil']);
                         }
                        
                        // si posee un edificio insertamos la session con su respectiva db
                        $valores = array('id_usuario'=>$listaUsuario[0]['id'],'id_db'=>$listDb[0]['id'],'ip_cliente'=>$_SERVER['REMOTE_ADDR'],'port_cliente'=>$_SERVER['REMOTE_PORT']);
                        $sid->offsetSet('idSession',$tsession->crearSesion($valores));
                        
                        //Mapeamos la base de datos
                            $sid->offsetSet('dbNombre',$listDb[0]['nombre_db']);                                                     
                            $modulo = $usuario->getModulo($this->dbAdapter,$listDb[0]['id_perfil']);
                            $sid->offsetSet('modulo', $modulo);
                           
                             if(count($modulo) > 1){
                                $urlHome = 'application';
                             }else{
                                $urlHome = $modulo[0]['url'];
                             }
                             $sid->offsetSet('urlHome',$urlHome);
                             
                             return $this->redirect()->toUrl($this->getRequest()->getBaseUrl().'/'.$urlHome);
                        
                        
                    }
                    
                     }
                     
                else{
                    return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>2));
                }
                
            }else{
                return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
            }
            
            
       }else{
            return $this->forward()->dispatch('Application\Controller\Login',array('action'=>'index','id'=>1));
        }
        
        
        
        
        
        return new ViewModel(array('data'=>$idSession));
    }    
    
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
        $usu = new PersonaTable($this->dbAdapter);
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
             $url = "www.becheck.cl/pmv/public/application/recuperar/index/".$token;                                          
             $htmlMarkup = \HtmlCorreo::htmlPassword($existe[0]['nombre']." ".$existe[0]['apellido'],$url);                                       
             
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
             ->addFrom('soporte@becheck.cl', 'Sistema be check')
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
       // llamamos la session para obtener el IDSession del aplicativo
       $sid = new Container('base');
       $idSession = $sid->offsetGet('idSession');
       
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
        
       
    }    
     public function landingcontactoAction()
    {
        
            $descripcion="test 1!!";    
          $result = new JsonModel(array('descripcion'=>$descripcion));                                
          return $result;
    }                           
    
}
