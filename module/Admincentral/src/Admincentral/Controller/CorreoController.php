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


use Sistema\Model\Entity\Crm\ProspectoDetalleTable;
use Sistema\Model\Entity\Crm\CampanaTable;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;


class CorreoController extends AbstractActionController
{
    public function indexAction()
    {        
       //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas         
        $campana = new CampanaTable($this->dbAdapter);
        
       
       
       
       
       
       
       
       
       $this->layout('layout/admincentral');
        return new ViewModel();
        
        
    }
    
     public function enviarAction()
    {
                    
        //Obtenemos datos POST        
        $lista = $this->request->getPost();
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas         
        $prosdetalle = new ProspectoDetalleTable($this->dbAdapter);
        $correo = $prosdetalle->getCorreoSedeCampana($this->dbAdapter,$lista['COD_SEDE'],$lista['ID_CAMPANA']);
                                                                                   
            //Enviamos correo a cada prospecto 
            for($i=0;$i<count($correo);$i++){
                
                $html->type = "text/html";
                $body = $lista['CUERPO'];                
                $message = new Message();
                $message->addTo($correo[$i]['correo'])
                ->addFrom('contacto@laaraucana.cl', 'La Araucana')
                ->setSubject('Contacto de La araucana')
                ->setBody($body);
                $transport = new SendmailTransport();
                $transport->send($message);
                                                                
                sleep(2);        
            }                                                                                                                       
        
        $this->layout('layout/admincentral');
        return new JsonModel(array('descr'=>'Correos enviados exitosamente'));
        
        
    }
    
   
}
