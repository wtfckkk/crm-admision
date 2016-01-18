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
use Sistema\Model\Entity\Crm\SedeTable;
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
        $sede = new SedeTable($this->dbAdapter);                
        $sedes = $sede->getSedes();                                                                   
                  
        $result = new ViewModel(array('sedes'=>$sedes));
        $result->setTerminal(true);  
        return $result;
        
        
    }
    
    public function getcampanasAction()
    {        
       //Obtenemos datos POST        
        $lista = $this->request->getPost();
       
       //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        
        //Tablas         
        $campana = new CampanaTable($this->dbAdapter);
        $campanas = $campana->getCampanaXsede($this->dbAdapter,$lista['COD_SEDE']);  
        
        //Retornamos a la vista                                                                                                
        return new JsonModel(array('campanas'=>$campanas));
        
        
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
                  
               /*   $text = new MimePart($textContent);
             $text->type = "text/plain";*/

             $html = new MimePart($lista['CUERPO']);
             $html->type = "text/html";

            /* $image = new MimePart(fopen($pathToImage, 'r'));
             $image->type = "image/jpeg";*/

             $body = new MimeMessage();
             $body->setParts(array($html));
        
             $message = new Message();
             $message->addTo($correo[$i]['CORREO'])
             ->setEncoding("UTF-8")
             ->addFrom('<admision@iplaaraucana.cl>', 'Admisión 2016')
             ->setSubject('Instituto Profesional La Araucana')
             ->setBody($body);

             $transport = new SendmailTransport();
             $transport->send($message);                                             
                
                                                                
                sleep(2);      
            }                                                                                                                       
        
        $this->layout('layout/admincentral');
        return new JsonModel(array('descr'=>count($correo).' correos enviados exitosamente'));
        
        
    }
    
   
}
