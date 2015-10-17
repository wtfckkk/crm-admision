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
        return new ViewModel();
        
        
    }
    
    public function cargaprospectosAction()
    {
            //Definimos ruta DEBE EXISTIR PREVIAMENTE
            $ruta = $_SERVER['DOCUMENT_ROOT'].'/crm/upload2s'; 
                                 
            //Obtenemos y guardamos File    
            $file = $this->params()->fromFiles();                                
            $adapterFile = new \Zend\File\Transfer\Adapter\Http();
            $adapterFile->setDestination($ruta);
            $adapterFile->receive($file['file-0']['name']);
            
            //Buscamos el file
            $inputFileName = $ruta."/".$file['file-0']['name'];
        
        
        /*
        
        //Obtenemos clase PHPExcel
            $objPHPExcel = new \PHPExcel();
            
            //Buscamos el file
            $inputFileName = $_SERVER['DOCUMENT_ROOT'].'/files/db/'.$id_db.'/'.$modulo[0]['url'].'/copropietario/Copropietario.xlsx';            
             
            //Llamamos Clase PHPExcel
            $objReader = \PHPExcel_IOFactory::createReader('Excel2007');
            $objReader->setReadDataOnly(TRUE);
            $objPHPExcel = $objReader->load($inputFileName);
            //Obtenemos Hoja de trabajo
            $objWorksheet = $objPHPExcel->getActiveSheet();

            //Insertamos valores en celdas
            for($i=3;$i<count($unidades)+3;$i++){
                $objWorksheet->setCellValue('A'.$i, $unidades[0]['nombre']);   
            } */
            
            $this->layout('layout/admincentral');
        $result = new JsonModel(array('test'=>$inputFileName));
        return $result;
        
        
    }
    
    public function oportunidadesAction()
    {
        
        $this->layout('layout/admincentral');
        return new ViewModel();
        
        
    }
    
    
   
}
