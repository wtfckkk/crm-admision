<?php

namespace Application\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Adapter\Adapter;

use Sistema\Model\Entity\Crm\CarrerasTable;


class IndexController extends AbstractActionController
{
     public function indexAction()
    {
        //Conectamos a BBDD                                             
        //$this->dbAdapter=$this->getServiceLocator()->get('crm');      
        $this->dbAdapter=$this->getServiceLocator()->get('Zend\Db\Adapter');
        
       $test = new CarrerasTable($this->dbAdapter);        
                             
        $valores =  $test->getDatos($this->dbAdapter);                
        
         $this->layout('layout/layout');                       
        return new ViewModel(array('valores'=>$valores));
        $result->setTerminal(true); 
        return $result; 
    }
}
