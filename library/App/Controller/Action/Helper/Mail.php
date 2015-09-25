<?php

class App_Controller_Action_Helper_Mail extends Zend_Controller_Action_Helper_Abstract {

    /**
     *
     * @var Zend_Mail
     */
    private $_mail;

    public function __call($name, $arguments) {
        try {
            
           
            $this->_mail = new Zend_Mail('utf-8');
            $options = $arguments[0];
            $f = new Zend_Filter_Word_CamelCaseToDash();
            $tplDir = APPLICATION_PATH . '/entitys/mailing/templates/';

            $mailView = new Zend_View();
            $layoutView = new Zend_View();
            $mailView->setScriptPath($tplDir);
            $layoutView->setScriptPath($tplDir);

            $template = strtolower($f->filter($name)) . '.phtml';

            if (!is_readable(realpath($tplDir . $template))) {
                throw new Zend_Mail_Exception('No existe template para este email');
            }

            $mailConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/mail.ini', 'mail');
            $mailConfig = $mailConfig->toArray();
            $paramsTemplate = array();
            if (array_key_exists($name, $mailConfig)) {
                $paramsTemplate = $mailConfig[$name];
            } else {
                throw new Zend_Mail_Exception('No existe configuración para este template. Verificar mailing.ini');
            }

            $smtpHost = $mailConfig['mailServer'];

            //$smtpConf = $mailConfig['connectionData'][$paramsTemplate['sendAccount']];
            $smtpConf = $mailConfig['connectionData'];
            $transport = new Zend_Mail_Transport_Smtp($smtpHost, $smtpConf);

            if (!array_key_exists('subject', $paramsTemplate) || trim($paramsTemplate['subject']) == "") {
                throw new Zend_Mail_Exception('Subject no puede ser vacío, verificar mailing.ini');
            } else {
                $options['subject'] = $paramsTemplate['subject'];

                if (isset($paramsTemplate['from'])) {
                    $options['from'] = $paramsTemplate['from'];
                    $options['fromName'] = $paramsTemplate['fromName'];
                } else {
                    $options['from'] = $mailConfig['defaults']['from'];
                    $options['fromName'] = $mailConfig['defaults']['fromName'];
                }
            }

            if (!array_key_exists('to', $options)) {
                $options['to'] = $mailConfig['defaults']['from'];


                // throw new Zend_Mail_Exception('Falta indicar destinatario en $options["to"]');
            } else {
                $v = new Zend_Validate_EmailAddress();
                //            if (!$v->isValid($options['to'])) {
                //                //throw new Zend_Mail_Exception('Email inválido');
                //                // En lugar de lanzar un error, mejor lo logeo.
                //                $log = Zend_Registry::get('log');
                //                $log->warn('Email inválido: ' . $options['to']);
                //            }
            }
            foreach ($options as $key => $value) {
                $mailView->assign($key, $value);

                if (!is_array($value)) {
                    $options['subject'] = str_replace('{%' . $key . '%}', $value, $options['subject']);
                    $template = str_replace('{%' . $key . '%}', $value, $template);
                }
            }
            $mailView->mailData = $options;
            $layoutView->mailData = $options;
            //echo $template; exit;

            $mailView->addHelperPath('App/View/Helper', 'App_View_Helper');
            $layoutView->addHelperPath('App/View/Helper', 'App_View_Helper');

            $mailViewHtml = $mailView->render($template);

            //var_dump($transport);exit;
            //echo $mailViewHtml; exit;
            //$layoutView->assign('emailTemplate', $mailViewHtml);
            //$mailHtml = $layoutView->render('_layout.phtml');
            $this->_mail->setBodyHtml($mailViewHtml);
            $this->_mail->setFrom($options['from'], $options['fromName']);
            $this->_mail->addTo($options['to']);
            if (isset($paramsTemplate['Cc'])) {
                $Cc = explode(",", $paramsTemplate['Cc']);
                $this->_mail->addCc($Cc);
            }
//            $this->_mail->setSubject($options['subject']);
            $this->_mail->setSubject($options['asunto']);
            $this->_mail->send($transport);

            $options['dataMailing']['from'] = $options['from'];
            $options['dataMailing']['subject'] = $options['subject'];
            $options['dataMailing']['template'] = $name;

//            $modelMail = new Mailing_Model_Mailing();
//            $modelMail->save($options['dataMailing']);
        } catch (Exception $ex) {
            $stream = @fopen(LOG_PATH . '/mailerror.log', 'a+', false);
            if (!$stream) {
                echo "Error al abrir log.";
            }
            $writer = new Zend_Log_Writer_Stream(LOG_PATH . '/mailerror.log');
            $logger = new Zend_Log($writer);
            $logger->info('***********************************');
            $logger->info('Template: ' . $name);
            $logger->info('Mail Data: ' . Zend_Json_Encoder::encode($arguments));
            $logger->info('Error Message: ' . $ex->getMessage());
            $logger->info('***********************************');
            $logger->info('');
        }
    }

}
