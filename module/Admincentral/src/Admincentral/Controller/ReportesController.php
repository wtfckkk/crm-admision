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

use Sistema\Model\Entity\Crm\OportunidadTable;

use Zend\Mail\Message;
use Zend\Mail\Transport\Sendmail as SendmailTransport;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use Zend\Mime\Message as MimeMessage;
use Zend\Mime\Part as MimePart;


class ReportesController extends AbstractActionController
{
    public function roportunidadesAction()
    {        
       //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas         
                                                                            
        $result = new ViewModel();
        $result->setTerminal(true); 
        return $result;
        
        
    }
    
    public function exceloportunidadesAction()
    {
        //Conectamos con BBDD
        $this->dbAdapter=$this->getServiceLocator()->get('Zend/Db/Adapter'); 
        //Tablas         
        $opor = new OportunidadTable($this->dbAdapter);                
        $oportunidades = $opor->getOportunidadesFull($this->dbAdapter);     
        $sid = new Container('base');
        
         //Creamos excel
        $objPHPExcel = new \PHPExcel();
        $campos = array_keys($oportunidades[0]);   

        // Set document properties
        $objPHPExcel->getProperties()->setCreator("CRM La Araucana")
                             ->setTitle("Reporte de Oportunidades")                             
                             ->setDescription("Reporte total de oportunidades")
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
                                
            //Alineamos y coloreamos excel
            $ews = $objPHPExcel->getSheet(0);
            $header = 'a1:p1';
            $ews->getStyle($header)->getFill()->setFillType(\PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setARGB('00ffff00');
            $style = array(
               'font' => array('bold' => true,),
               'alignment' => array('horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,),
               );
            $ews->getStyle($header)->applyFromArray($style);
            for ($col = ord('a'); $col <= ord('p'); $col++)
            {
                $ews->getColumnDimension(chr($col))->setAutoSize(true);
            }

            // Agregamos array al excel
            $objPHPExcel->setActiveSheetIndex(0);
            $objPHPExcel->getActiveSheet()->fromArray($oportunidades, null, 'A2');
            
            //Grabamos Excel
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
            $filename='/var/www/html/crm/excel/agendamientos/Oportunidades_'.$sid->offsetGet('usuario').'_'.date('YmdGis').'.xlsx';
            $objWriter->save($filename);  
            
            //Retornamos a la Vista
            $desc = "Se ha generado planilla excel con ".count($oportunidades)." oportunidades";                                                                                             
            return new JsonModel(array(
                            'ruta'=>str_replace("/var/www/html","",$filename),
                            'status'=>'ok',
                            'desc'=>$desc,
                             )); 
    }
}
