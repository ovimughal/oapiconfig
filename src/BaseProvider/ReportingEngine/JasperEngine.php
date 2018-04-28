<?php
use Oapiconfig\DI\ServiceInjector;

require (ServiceInjector::oFileManager()->getConfigValue('java_bridge'));

function executeJasper($sqlQuery, $reportTemplate, $parameters = [], $subReportParameters = [], $outputFormat = 'pdf')
{
    try {
        // Connect to Database
        $conn = dataBaseConnection();
        // Set Parameters if any
        $params = setParameters($parameters, $subReportParameters);
        // Load Report Remplate & Compile Report with Query
        $report = loadAndCompileReport($reportTemplate, $sqlQuery);

        // Instantiatle Java Fill Manager
        $fillManager = new JavaClass('net.sf.jasperreports.engine.JasperFillManager');
        // Fill with compiled Report, Params & database connection object
        $jasperPrint = $fillManager->fillReport($report, $params, $conn);

        // Export report to pdf, html, csv etc
        $result = exportOutput($jasperPrint, $outputFormat);


        //$url = $_SERVER['HTTP_REFERER'];
        //echo '<script>window.open(''.$url.'doc/output.'.$format.'');</script>';
        //chmod($outputPath, 0777);
        //readfile($outputPath);
        //chmod($outputPath, $mode)
        //unlink($outputPath);
    } catch (Exception $exc) {
        $result = new Exception('Execute Exception: '.$exc);
    }
    return $result;
}

function dataBaseConnection()
{
    try {

        $dbms = ServiceInjector::oFileManager()->getConfigValue('dbms');
        $dbmsServer = ServiceInjector::oFileManager()->getConfigValue('dbms_server');
        $dataBaseName = ServiceInjector::oFileManager()->getConfigValue('data_base_name');
        $dataBaseUser = ServiceInjector::oFileManager()->getConfigValue('data_base_user');
        $dataBasePassword = ServiceInjector::oFileManager()->getConfigValue('data_base_password');

        // Instantiate Java Lang class to set Database driver
        $class = new JavaClass('java.lang.Class');

        // Instantiate Java DriverManager for Database
        $driverManager = new JavaClass('java.sql.DriverManager');

        // switch to Database
        switch ($dbms) {
            case 'sqlsrv':
                // MS SQL Server Driver
                $class->forName('com.microsoft.sqlserver.jdbc.SQLServerDriver');
                // MS SQL Server Connection
                $conn = $driverManager->getConnection('jdbc:sqlserver://' . $dbmsServer . ';databaseName=' . $dataBaseName, $dataBaseUser, $dataBasePassword);
                break;
            case 'mysql':
                // MySql Driver
                $class->forName('com.mysql.jdbc.Driver');
                // MySQl Server Connection
                $conn = $driverManager->getConnection('jdbc:mysql://' . $dbmsServer . '/' . $dataBaseName . '?zeroDateTimeBehavior=convertToNull', $dataBaseUser, $dataBasePassword);
                break;
        }
    } catch (JavaException $exc) {
        throw new Exception('Database Connection Exception: ' . $exc);
    }
    return $conn;
}

function loadAndCompileReport($reportTemplate, $sqlQuery)
{
    try {
        // Load Report Template Directory
        $templateDir = getcwd().'/'.ServiceInjector::oFileManager()->getConfigValue('reporting_templates');

        // Instantiate Jasper XML Loader
        $jasperxml = new java('net.sf.jasperreports.engine.xml.JRXmlLoader');

        // Load Jasper Report .jrxml Template
        $jasperDesign = $jasperxml->load($templateDir . '/' . $reportTemplate);

        // Instantiate Jasper Query 
        $query = new java('net.sf.jasperreports.engine.design.JRDesignQuery');

        // Set Sql Query to be executed
        $query->setText($sqlQuery);
        $jasperDesign->setQuery($query);

        // Instantiate Jasper Compiler
        $compileManager = new JavaClass('net.sf.jasperreports.engine.JasperCompileManager');

        // Compile Report
        $report = $compileManager->compileReport($jasperDesign);
    } catch (JavaException $exc) {
        throw new Exception('Load And Compile Exception: ' . $exc);
    }

    return $report;
}

function setParameters($parameters = [], $subReportParameters = [])
{
    try {
        // Instantiate Java HashMap
        $params = new Java('java.util.HashMap');

        // Set Parameters
        //$params->put('title', 'Customer Profile');
        
        if(is_array($parameters)){
            if(count($parameters)){
                foreach ($parameters as $key => $value) {
                     $params->put($key, $value);
                }
            }
        }
        
        // for subreports
        if(is_array($subReportParameters)){
            if(count($subReportParameters)){
                $templateDir = getcwd().'/'.ServiceInjector::oFileManager()->getConfigValue('reporting_templates');
                foreach ($subReportParameters as $key => $value) {
                     $params->put($key, $templateDir . '/' . $value);
                }
            }
        }
    } catch (JavaException $exc) {
        throw new Exception('Parameters Exception: ' . $exc);
    }

    return $params;
}

function exportOutput($jasperPrint, $outputFormat = 'pdf')
{
    try {
        // Load Output Directory
        $outputDir = getcwd().'/'.ServiceInjector::oFileManager()->getConfigValue('reporting_output');

        // Instantiate Jasper Exporter
        $exporter = new java('net.sf.jasperreports.engine.JRExporter');

        // Switch which format to output
        // default format is pdf
        switch ($outputFormat) {
            case 'xls':
                $outputPath = $outputDir . '/output.xls';

                $exporter = new java('net.sf.jasperreports.engine.export.JRXlsExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRXlsExporterParameter')->IS_ONE_PAGE_PER_SHEET, java('java.lang.Boolean')->TRUE);
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRXlsExporterParameter')->IS_WHITE_PAGE_BACKGROUND, java('java.lang.Boolean')->FALSE);
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRXlsExporterParameter')->IS_REMOVE_EMPTY_SPACE_BETWEEN_ROWS, java('java.lang.Boolean')->TRUE);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //header('Content-type: application/vnd.ms-excel');
                //header('Content-Disposition: attachment; filename=output.xls');
                break;
            case 'csv':
                $outputPath = $outputDir . '/output.csv';

                $exporter = new java('net.sf.jasperreports.engine.export.JRCsvExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRCsvExporterParameter')->FIELD_DELIMITER, ',');
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRCsvExporterParameter')->RECORD_DELIMITER, '\n');
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRCsvExporterParameter')->CHARACTER_ENCODING, 'UTF-8');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //header('Content-type: application/csv');
                //header('Content-Disposition: attachment; filename=output.csv');
                break;
            case 'docx':
                $outputPath = $outputDir . '/output.docx';

                $exporter = new java('net.sf.jasperreports.engine.export.ooxml.JRDocxExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //header('Content-type: application/vnd.ms-word');
                //header('Content-Disposition: attachment; filename=output.docx');
                break;
            case 'html':
                $outputPath = $outputDir . '/output.html';

                $exporter = new java('net.sf.jasperreports.engine.export.HtmlExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                break;
            case 'pdf':
                $outputPath = $outputDir . '/output.pdf';

                $exporter = new java('net.sf.jasperreports.engine.export.JRPdfExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //        header('Content-type: application/pdf');
                //        header('Content-Disposition: inline; filename=output.pdf');
                break;
            case 'ods':
                $outputPath = $outputDir . '/output.ods';

                $exporter = new java('net.sf.jasperreports.engine.export.oasis.JROdsExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //        header('Content-type: application/vnd.oasis.opendocument.spreadsheet');
                //        header('Content-Disposition: attachment; filename=output.ods');
                break;
            case 'odt':
                $outputPath = $outputDir . '/output.odt';

                $exporter = new java('net.sf.jasperreports.engine.export.oasis.JROdtExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //        header('Content-type: application/vnd.oasis.opendocument.text');
                //        header('Content-Disposition: attachment; filename=output.odt');
                break;
            case 'txt':
                $outputPath = $outputDir . '/output.txt';

                $exporter = new java('net.sf.jasperreports.engine.export.JRTextExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRTextExporterParameter')->PAGE_WIDTH, 120);
                $exporter->setParameter(java('net.sf.jasperreports.engine.export.JRTextExporterParameter')->PAGE_HEIGHT, 60);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //        header('Content-type: text/plain');
                break;
            case 'rtf':
                $outputPath = $outputDir . '/output.rtf';

                $exporter = new java('net.sf.jasperreports.engine.export.JRRtfExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //        header('Content-type: application/rtf');
                //        header('Content-Disposition: attachment; filename=output.rtf');
                break;
            case 'pptx':
                $outputPath = $outputDir . '/output.pptx';

                $exporter = new java('net.sf.jasperreports.engine.export.ooxml.JRPptxExporter');
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->JASPER_PRINT, $jasperPrint);
                $exporter->setParameter(java('net.sf.jasperreports.engine.JRExporterParameter')->OUTPUT_FILE_NAME, $outputPath);

                //        header('Content-type: aapplication/vnd.ms-powerpoint');
                //        header('Content-Disposition: attachment; filename=output.pptx');
                break;
        }
        $exporter->exportReport();
        $ouputFileName = ServiceInjector::oFileManager()->getConfigValue('output_file_name');
        $routeResource = 'reporting_file_download_route';
        $result = ServiceInjector::oFileManager()->getFileDownloadLink($routeResource, $ouputFileName, $outputFormat);//'Report Generated Successfully';
    } catch (JavaException $exc) {
        throw new Exception('Export Output Exception: ' . $exc);
    }
    return $result;
}
