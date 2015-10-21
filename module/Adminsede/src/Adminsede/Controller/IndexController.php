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
use Sistema\Model\Entity\Crm\OportunidadTable;
use Sistema\Model\Entity\Crm\FeedbackTable;
use Sistema\Model\Entity\Crm\CampanaTable;


class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        
        //Tablas
        $feedback = new FeedbackTable($this->dbAdapter);
        $prospecto = new ProspectoCabeceraTable($this->dbAdapter);
        $oportunidad = new OportunidadTable($this->dbAdapter);
        $campana = new CampanaTable($this->dbAdapter);
        
        //
        $countProspecto    = $prospecto->countProspecto($this->dbAdapter);    
        $countFeedback     = $feedback->countFeedback($this->dbAdapter);   
        $countOportunidad  = $oportunidad->countOportunidades($this->dbAdapter);           
        $countCampana      = $campana->countCampana($this->dbAdapter); 
        
        $this->layout('layout/adminsede');
        return new ViewModel(array(
                            'countfeedback'=>$countFeedback[0]['count'],
                            'countprospecto'=>$countProspecto[0]['count'],
                            'countoportunidad'=>$countOportunidad[0]['count'],
                            'countcampana'=>$countCampana[0]['count'],
                            ));
        
        
    }
    
   
}
