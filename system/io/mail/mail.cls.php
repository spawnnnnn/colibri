<?php
    
    // enumerates          
                  
    class MailErrorMessages {
        const ProvideAddress = 'You must provide at least one recipient email address.';
        const MailerNotSupported = ' mailer is not supported.';
        const Execute = 'Could not execute: ';
        const Instantiate = 'Could not instantiate mail function.';
        const Authenticate = 'SMTP Error: Could not authenticate.';
        const FromFailed = 'The following From address failed: ';
        const EecipientsFailed = 'SMTP Error: The following recipients failed: ';
        const DataNotAccepted = 'SMTP Error: Data not accepted.';
        const ConnectHost = 'SMTP Error: Could not connect to SMTP host.';
        const FileAccess = 'Could not access file: ';
        const FileOpen = 'File Error: Could not open file: ';
        const Encoding = 'Unknown encoding: ';
        const Signing = 'Signing Error: ';
        const SmtpError = 'SMTP server error: ';
        const SmtpConnectFailed = 'SMTP connection failed: ';
        const EmptyMessage = 'Message body empty';
        const InvalidAddress = 'Invalid address';
        const VariableSet = 'Cannot set or reset variable: ';
        const InvalidArgument = 'Invalid argument: ';
        const RecipientsFailed = 'Recipients failed: ';
        const TLS = "Error connect with TLS";
        const SendError = "Error sending mail";
    }
    
    class MailerTypes {
        const Mail      = "mail";
        const SendMail  = "sendmail";
        const Smtp      = "smtp";
    }

    // providers 
    
    class MailAddressProvider extends ContentProvider {
    
        public static function GetData($dataId) {
            preg_match_all('/\s?([^\[]+)\s?\[([^>]+)\]/i', $dataId, $matches);
            if(count($matches) > 0) {
                $name = strtolower($matches[1][0]);
                $address = $matches[2][0];
                return new MailAddress($address, $name);
            }
            return null;
        }

    }
    
    // classes
    
    class MailException extends BaseException {
        
        public function ToString() {
            return '<strong>' . $this->message . "</strong><br />\n";
        }
        
    }

    class MailAddressList extends BaseList {
        
        public function __construct() {
            parent::__construct();
        }
        
        public function Add($a) {
            if(!($a instanceOf MailAddress)) 
                throw new MailException(MailErrorMessages::InvalidArgument, ExceptionTypes::StopCrytical);
            parent::Add($a);
        }
        
        public function AddRange($values) {
            foreach($values as $v)
                if(!($v instanceOf MailAddress))
                    throw new MailException(MailErrorMessages::InvalidAddress, ExceptionTypes::StopCrytical);
            parent::AddRange($values);
        }
        
        public function Join() {
            $ret = '';
            foreach ($this as $a)
                $ret .= ', '.$a->formated;
            return substr($ret, strlen(', '));
        }
        
    }
    
    class MailAddress {
        
        private $_address;
        private $_displayName;
        private $_charset;
        
        public function __construct($address, $displayName = '', $charset = 'utf-8') {
            
            $address = trim($address);
            $displayName = trim(preg_replace('/[\r\n]+/', '', $displayName)); //Strip breaks and trim
            
            $this->_address = $address;    
            $this->_displayName = $displayName;
            $this->_charset = $charset;
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'address':
                    return $this->_address;
                case 'name':
                    return $this->_displayName;
                case 'charset':
                    return $this->_charset;
                case 'formated':
                    return $this->_formatAddress();
            }
        }

        public function __set($property, $value) {
            switch(strtolower($property)) {
                case 'address':
                    $this->_address = trim($value);
                    break;
                case 'name':
                    $this->_displayName = trim(preg_replace('/[\r\n]+/', '', $value));
                    break;
                case 'charset':
                    $this->_charset = $value;
                    break;
            }
        }
        
        private function _formatAddress() {
            if (Variable::IsEmpty($this->_displayName))
                return Strings::StripNewLines($this->_address);
            else
                return Strings::EncodeHeader(Strings::StripNewLines($this->_displayName), 'phrase', $this->_charset) . ' <' . Strings::StripNewLines($this->_address) . '>';
        }        
        
    }
    
    class MailAttachmentList extends BaseList {
                             
        public function __construct() {
            parent::__construct();
        }
        
        public function Add($a) {
            if(!($a instanceOf MailAttachment)) 
                throw new MailException(MailErrorMessages::InvalidArgument, ExceptionTypes::StopCrytical);
                
            parent::Add($a);
        }
        
        public function AddRange($values) {
            foreach($values as $v)
                if(!($v instanceOf MailAttachment))
                    throw new MailException(MailErrorMessages::InvalidArgument, ExceptionTypes::StopCrytical);
            parent::AddRange($values);
        }

        public function HasInline() {
            foreach($this as $a)
                if($a->isInline) 
                    return true;
            return false;
        }        
        
    }
    
    class MailAttachment {
        
        private $_path; // file path or string (if attachment is string type)
        private $_filename; // file name
        private $_charset; // file encoding
        private $_encoding; // file encoding
        private $_name; // attachment name
        private $_type; // mime type
        private $_cid; // ??
        private $_isString; // is attachemtn is string
        private $_isInline; // is inline image
        
        public function __construct() { }
        
        public function __get($property) {
            $name = "_".$property;
            return $this->$name;
        }
        
        public function __set($property, $value) {
            $name = "_".$property;
            $this->$name = $value;
        }
        
        public static function Create($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream', $charset = '') {
            $ma = new MailAttachment();

            if(!FileInfo::Exists($path))
                throw new MailException(MailErrorMessages::FileAccess.$path, ExceptionTypes::StopCrytical);
            
            $filename = basename($path);
            if($name == '')
                $name = $filename;

            $ma->path = $path;
            $ma->filename = $filename;
            $ma->charset = $charset;
            $ma->name = $name;
            $ma->encoding = $encoding;
            $ma->type = $type;
            $ma->cid = 0;
            $ma->isString = false;
            $ma->isInline = false;
                    
            return $ma;
        }
        
        public static function CreateString($string, $filename, $encoding = 'base64', $type = 'application/octet-stream', $charset = '') {
            $ma = new MailAttachment();
            
            $ma->path = $string;
            $ma->filename = $filename;
            $ma->charset = $charset;
            $ma->name = basename($filename);
            $ma->encoding = $encoding;
            $ma->type = $type;          
            $ma->cid = 0;
            $ma->isString = true;
            $ma->isInline = false;
            
            return $ma;
        }        
        
        public function CreateEmbeded($path, $cid, $name = '', $encoding = 'base64', $type = 'application/octet-stream', $charset = '') {

            $ma = new MailAttachment();
            
            if(!FileInfo::Exists($path))
                throw new MailException(MailErrorMessages::FileAccess.$path, ExceptionTypes::StopCrytical);

            $filename = basename($path);
            if($name == '')
                $name = $filename;

            $ma->path = $path;
            $ma->filename = $filename;
            $ma->charset = $charset;
            $ma->name = $name;
            $ma->encoding = $encoding;
            $ma->type = $type;
            $ma->cid = $cid;
            $ma->isString = false;
            $ma->isInline = true;

            return $ma;
        }        
        
    }
    
    class MailMessage {
        
        private $_id;
        private $_priority           = 3;
        private $_charset            = 'iso-8859-1';
        private $_contenttype        = 'text/plain';
        private $_errorinfo          = '';

        private $_encoding           = '8bit';
        private $_from               = null; // mailaddress
        private $_subject            = '';
        private $_body               = '';
        private $_altbody            = '';
        private $_wordwrap           = 0;
        private $_confirmreadingto   = '';
        private $_returnpath         = '';
        
        private $_to                 = null; // array of mailaddress
        private $_cc                 = null; // array of mailaddress
        private $_bcc                = null; // array of mailaddress
        private $_replyto            = null; // array of mailaddress
        
        private $_attachments         = null;
        
        private $_customheader       = array();
        
        public function __construct($from = null, $to = null, $subject = '') {
            
            $this->_to = new MailAddressList();
            $this->_cc = new MailAddressList();
            $this->_bcc = new MailAddressList();
            $this->_replyto = new MailAddressList();
            
            if(!Variable::IsNull($from)) {
                if($from instanceOf MailAddress)
                    $this->_from = $from;
                else {
                    // распарсить строку, если нужно
                    $this->_from = new MailAddress($from);
                }
            }
            
            if(!Variable::IsNull($to)) {
                if($to instanceOf MailAddress) 
                    $this->_to->Add($to);
                elseif(Variable::IsArray($to)) {
                    $this->_to->AddRange($to);
                }
                elseif(Variable::IsString($to)) {
                    $emails = explode(';', $to);
                    foreach ($emails as $k => $v) {
                        $this->_to->Add(new MailAddress($v));
                    }
                }
            }
            
            if(!Variable::IsEmpty($subject)) {
                $this->_subject = $subject;
            }
            
            $this->_attachments = new MailAttachmentList();
            
        }
        
        public function __get($property) {
            // это все пока что           
            // сделать свойство ishtml
            switch(strtolower($property)) {
                case 'type':
                    if($this->_attachments->count < 1 && strlen($this->_altbody) < 1)
                        return 'plain';
                    else {
                        if($this->_attachments->count > 0)
                            return 'attachments';
                        if(strlen($this->_altbody) > 0 && $this->_attachments->count < 1)
                            return 'alt';
                        if(strlen($this->_altbody) > 0 && $this->_attachments->count > 0)
                            return 'alt_attachments';
                    }                
                default:
                    $name = "_".strtolower($property);
                    return $this->$name;   
            }
            
        }
        
        public function __set($property, $value) {
            switch(strtolower($property)) {
                case 'altbody':
                    // Set whether the message is multipart/alternative
                    if(!Variable::IsEmpty($value))
                        $this->_contenttype = 'multipart/alternative';
                    else
                        $this->_contenttype = "text/plain";
            }
            $name = "_".strtolower($property);
            $this->$name = $value;
        }
        
        public function IncludeEmbededImages($message, $basedir = '') {
            
            preg_match_all("/(src|background)=\"(.*)\"/Ui", $message, $images);
            
            if(isset($images[2])) {
                
                foreach($images[2] as $i => $url) {
                    
                    // do not change urls for absolute images (thanks to corvuscorax)
                    if(!preg_match('#^[A-z]+://#', $url)) {
                        
                        $filename = basename($url);
                        
                        $directory = dirname($url);
                        if($directory == '.') 
                            $directory = '';
                            
                        $cid = 'cid:' . md5($filename);
                        $ext = pathinfo($filename, PATHINFO_EXTENSION);
                        $mimeType  = MimeType::FromExt($ext);
                        
                        if ( strlen($basedir) > 1 && substr($basedir,-1) != '/') 
                            $basedir .= '/'; 
                        
                        if ( strlen($directory) > 1 && substr($directory,-1) != '/') 
                            $directory .= '/'; 
                        
                        $ma = MailAttachment::CreateEmbeded($basedir.$directory.$filename, md5($filename), $filename, 'base64',$mimeType);
                        if($ma)
                            $message = preg_replace("/".$images[1][$i]."=\"".preg_quote($url, '/')."\"/Ui", $images[1][$i]."=\"".$cid."\"", $message);
                        
                        $this->_attachments->Add($ma);
                        
                    }
                    
                }
                
            }
            
            
            $this->IsHTML(true);
            $this->Body = $message;
            $textMsg = trim(strip_tags(preg_replace('/<(head|title|style|script)[^>]*>.*?<\/\\1>/s','',$message)));
            if (!empty($textMsg) && empty($this->AltBody)) {
                $this->AltBody = html_entity_decode($textMsg);
            }
            if (empty($this->AltBody)) {
                $this->AltBody = 'To view this email message, open it in a program that understands HTML!' . "\n\n";
            }
        }        
        
        
        
    }
    
    class MailCertificate {
        private   $_cert_file = "";
        private   $_key_file  = "";
        private   $_key_pass  = "";
        
        public function __construct($file, $keyfile, $keypass) {
            $this->_cert_file = $file;
            $this->_key_file = $keyfile;
            $this->_key_pass = $keypass;
        }
        
        public function __get($property) {
            switch(strtolower($property)) {
                case 'file':
                    return $this->_cert_file;
                case 'key':
                    return $this->_key_file;
                case 'pass':
                    return $this->_key_pass;
            }
        }
        
        
    }
    
    class MailDKIM {
        
        private $_selector   = 'phpmailer';
        private $_identity   = '';
        private $_domain     = '';
        private $_private    = '';
        
        public function __construct($selector, $identity, $domain, $private) {
            $this->_selector = $selector;
            $this->_identity = $identity;
            $this->_domain = $domain;
            $this->_private = $private;
        }
        
        public function __get($property) {
            $name = "_".strtolower($property);
            return $this->$name;
        }
        
        public function __set($property, $value) {
            $name = "_".strtolower($property);
            $this->$name = $value;
        }
        
        /**
        * Set the private key file and password to sign the message.
        *
        * @access public
        * @param string $key_filename Parameter File Name
        * @param string $key_pass Password for private key
        */
        private function QP($txt) {
            $tmp=""; $line="";
            for($i=0; $i<strlen($txt); $i++) {
                $ord = ord($txt[$i]);
                if ( ((0x21 <= $ord) && ($ord <= 0x3A)) || $ord == 0x3C || ((0x3E <= $ord) && ($ord <= 0x7E)) ) 
                    $line.=$txt[$i];
                else
                    $line.="=".sprintf("%02X",$ord);
            }
            return $line;
        }

        /**
        * Generate DKIM signature
        *
        * @access public
        * @param string $s Header
        */
        private function Sign($s) {
            $privKeyStr = file_get_contents($this->_private);
            if ($this->_passphrase!='') {
                $privKey = openssl_pkey_get_private($privKeyStr, $this->_passphrase);
            } else {
                $privKey = $privKeyStr;
            }
            if (openssl_sign($s, $signature, $privKey)) {
                return base64_encode($signature);
            }
            return false;
        }

        /**
        * Generate DKIM Canonicalization Header
        *
        * @access public
        * @param string $s Header
        */
        private function HeaderC($s) {
            $s = preg_replace("/\r\n\s+/"," ", $s);
            $lines = explode(Strings::CRLF, $s);
            foreach($lines as $key => $line) {
                list($heading, $value)=explode(":", $line, 2);
                $heading = strtolower($heading);
                $value = preg_replace("/\s+/"," ",$value) ; // Compress useless spaces
                $lines[$key] = $heading.":".trim($value) ; // Don't forget to remove WSP around the value
            }
            $s = implode("\r\n",$lines);
            return $s;
        }

        /**
        * Generate DKIM Canonicalization Body
        *
        * @access public
        * @param string $body Message Body
        */
        private function BodyC($body) {
            if ($body == '') return Strings::CRLF;
            $body = str_replace(Strings::CRLF, Strings::LF, $body);
            $body = str_replace(Strings::LF, Strings::CRLF, $body);
            while(substr($body,strlen($body)-4,4) == Strings::CRLF.Strings::CRLF)
                $body = substr($body, 0, strlen($body)-2);
            return $body;
        }
        
        /**
        * Create the DKIM header, body, as new header
        *
        * @access public
        * @param string $headers_line Header lines
        * @param string $subject Subject
        * @param string $body Body
        */
        public function Add($headers, $subject, $body) {
            
            $DKIMsignatureType    = 'rsa-sha1'; // Signature & hash algorithms
            $DKIMcanonicalization = 'relaxed/simple'; // Canonicalization of header/body
            $DKIMquery            = 'dns/txt'; // Query method
            $DKIMtime             = time() ; // Signature Timestamp = seconds since 00:00:00 - Jan 1, 1970 (UTC time zone)
            $subject_header       = "Subject: $subject";
            $headers              = explode(Strings::CRLF, $headers);
            
            foreach($headers as $header) {
                if (strpos($header,  'From:') === 0) 
                    $from_header = $header;
                else if (strpos($header, 'To:') === 0) 
                    $to_header = $header;
            }
            
            $from     = str_replace('|','=7C',$this->QP($from_header));
            $to       = str_replace('|','=7C',$this->QP($to_header));
            $subject  = str_replace('|','=7C',$this->QP($subject_header)) ; // Copied header fields (dkim-quoted-printable
            
            $body     = $this->BodyC($body);
            
            $DKIMlen  = strlen($body) ; // Length of body
            $DKIMb64  = base64_encode(pack("H*", sha1($body))) ; // Base64 of packed binary SHA-1 hash of body
            $ident    = ($this->_identity == '')? '' : " i=" . $this->_identity . ";";
            $dkimhdrs = "DKIM-Signature: v=1; a=" . $DKIMsignatureType . "; q=" . $DKIMquery . "; l=" . $DKIMlen . "; s=" . $this->_selector . ";\r\n".
                        "\tt=" . $DKIMtime . "; c=" . $DKIMcanonicalization . ";\r\n".
                        "\th=From:To:Subject;\r\n".
                        "\td=" . $this->_domain . ";" . $ident . "\r\n".
                        "\tz=$from\r\n".
                        "\t|$to\r\n".
                        "\t|$subject;\r\n".
                        "\tbh=" . $DKIMb64 . ";\r\n".
                        "\tb=";
            $toSign   = $this->HeaderC($from_header . "\r\n" . $to_header . "\r\n" . $subject_header . "\r\n" . $dkimhdrs);
            $signed   = $this->Sign($toSign);
            return "X-PHPMAILER-DKIM: phpmailer.worxware.com\r\n".$dkimhdrs.$signed."\r\n";
        }        

    }
    
    class MailSender {

        const Version           = '5.1';
        
        public $DOMAIN          = '';

        public $Mailer          = MailerTypes::Mail;
        
        public $Sendmail        = '/usr/sbin/sendmail';
        
        public $Sender          = '';
        
        public $Host            = 'localhost';
        public $Port            = 25;
        public $Helo            = '';
        public $Username        = '';
        public $Password        = '';
        public $Timeout         = 10;
        
        public $SMTPSecure      = '';
        public $SMTPAuth        = false;
        public $SMTPDebug       = false;
        public $SMTPKeepAlive   = false;
        
        public $DKIM            = null;
        public $certificate     = null;
        

        /**
        * Callback Action function name
        * the function that handles the result of the send email action. Parameters:
        *   bool    $result        result of the send action
        *   string  $to            email address of the recipient
        *   string  $cc            cc email addresses
        *   string  $bcc           bcc email addresses
        *   string  $subject       the subject
        *   string  $body          the email body
        * @var string
        */
        public $callback        = ''; //'callbackAction';

        /////////////////////////////////////////////////
        // PROPERTIES, PRIVATE AND PROTECTED
        /////////////////////////////////////////////////

        private   $smtp         = NULL;
        private   $boundary     = array();
        
        /////////////////////////////////////////////////
        // METHODS, VARIABLES
        /////////////////////////////////////////////////

        /**
        * Constructor
        * @param boolean $exceptions Should we throw external exceptions?
        */
        public function __construct($mailer) {
            $this->Mailer = $mailer;
        }
        
        public static function Create($mailer, $protos = false, $domain = '') {

            $protocols = Config::Load($protos ? $protos : PROTOCOLS);
            $mail = $protocols->mail;
            
            $sender = new MailSender($mailer);
            if($mailer == MailerTypes::Smtp) {
                $smtp = $mail->smtp;                
                
                $sender->Host = $smtp->attributes->host->value;
                $sender->Port = $smtp->attributes->port->value;
                $sender->SMTPAuth = $smtp->attributes->auth->value;
                $sender->SMTPSecure = $smtp->attributes->secure->value;
                $sender->Timeout = $smtp->attributes->timeout->value;
                if($sender->SMTPAuth) {
                    $sender->Username = $smtp->attributes->usr->value;
                    $sender->Password = $smtp->attributes->pwd->value;
                }
            }
            else if ($mailer == MailerTypes::Mail) {
                $mailNode = $mail->mail;                
                $sender->Username = $mailNode->attributes->from->value;
                $sender->Sender = $mailNode->attributes->from->value;
                $sender->IsMail();
            }
            else if ($mailer == MailerTypes::SendMail) {
                $mailNode = $mail->mail;                
                $sender->Username = $mailNode->attributes->from->value;
                $sender->Sender = $mailNode->attributes->from->value;
                $sender->IsSendmail();
            }
            
            $sender->DOMAIN = $domain;
            
            return $sender;
        }
        
        public function __get($property) {
            return null;
        }

        /**
        * Sets Mailer to send message using SMTP.
        * @return void
        */
        public function IsSMTP() {
            $this->Mailer = 'smtp';
        }

        /**
        * Sets Mailer to send message using PHP mail() function.
        * @return void
        */
        public function IsMail() {
            $this->Mailer = 'mail';
        }

        /**
        * Sets Mailer to send message using the $Sendmail program.
        * @return void
        */
        public function IsSendmail() {
            if (!stristr(ini_get('sendmail_path'), 'sendmail'))
                $this->Sendmail = '/var/qmail/bin/sendmail';
            $this->Mailer = 'sendmail';
        }

        /**
        * Sets Mailer to send message using the qmail MTA.
        * @return void
        */
        public function IsQmail() {
            if (stristr(ini_get('sendmail_path'), 'qmail'))
                $this->Sendmail = '/var/qmail/bin/sendmail';
            $this->Mailer = 'sendmail';
        }

      
        /**
        * Creates message and assigns Mailer. If the message is
        * not sent successfully then it returns false.  Use the ErrorInfo
        * variable to view description of the error.
        * @return bool
        */
        public function Send(MailMessage $m) {
            
            if($m->to->count + $m->cc->count + $m->bcc->count < 1)
                throw new MailException(MailErrorMessages::ProvideAddress, ExceptionTypes::StopCrytical);
            
            $header = $this->_createHeader($m);
            $body = $this->_createBody($m);

            
            //if (Variable::IsEmpty($m->body)) 
            //    throw new MailException(MailErrorMessages::EmptyMessage, ExceptionTypes::StopCrytical);

            // digitally sign with DKIM if enabled
            /*if ($this->DKIM)
                $header = str_replace(Strings::CRLF, Strings::LE, $this->DKIM->Add($header, $m->subject, $body)) . $header;*/
            // $header = 'DKIM-Signature: v=1; a=rsa-sha256; c=relaxed/relaxed; d=irepeater.com; s=google; h=mime-version:x-originating-ip:date:message-id:subject:from:to :content-type; bh=fQ8bg4Y+512YBeWHLEZrugsS03lZaKqCQK5ExTi+QYM=; b=P2Jh6p7D8YFkEqyw0jbxGpL4dE9cXLOU2d5O10tSJ+08lZuTSKDuZVtddXyCfSzlV8 0yGeehvzhI9ljdFxI6P0uKzbX2VQFFI16hXcyIIC8hqWOl9SvScugEsdEGEwoGF9wy90 xBOpkXPptqeoKKsJRxQR0PxSe4P1DOHne9g7E='.$header;
            
            // Choose the mailer and send through it
            
            switch($this->Mailer) {
                case 'sendmail':
                // не тестировалось
                    return $this->SendmailSend($m, $header, $body);
                case 'smtp':
                    return $this->SmtpSend($m, $header, $body);
                default:
                // не тестировалось
                    return $this->MailSend($m, $header, $body);
            }

        }

        private function SendmailSend(MailMessage $m, $header, $body) {
            
            if ($this->Sender != '')
                $sendmail = sprintf("%s -oi -f %s -t", escapeshellcmd($this->Sendmail), escapeshellarg($this->Sender));
            else
                $sendmail = sprintf("%s -oi -t", escapeshellcmd($this->Sendmail));
            
            // исправить - из MailAddress-а только адрес используется 
            foreach ($m->to as $val) {
                
                if(!@$mail = popen($sendmail, 'w'))
                    throw new MailException(MailErrorMessages::Execute.$this->Sendmail, ExceptionTypes::StopCrytical);
                
                fputs($mail, "To: " . $val->address . "\n");
                fputs($mail, $header);
                fputs($mail, $body);
                $result = pclose($mail);
                
                // implement call back function if it exists
                $this->doCallback(($result == 0) ? 1 : 0, $val, $m->cc, $m->bcc, $m->Subject, $body);
                
                if($result != 0)
                    throw new MailException(MailErrorMessages::Execute.$this->Sendmail, ExceptionTypes::StopCrytical);
            }
            
        
            return true;
        }

        private function MailSend(MailMessage $m, $header, $body) {
            $toArr = array();
            
            foreach($m->to as $t) 
                $toArr[] = $t->formated;
            
            $to = implode(', ', $toArr);

            $params = sprintf("-oi -f %s", $this->Sender);
                
            $old_from = ini_get('sendmail_from');
            @ini_set('sendmail_from', $this->Sender);
            
            /*if ($this->SingleTo === true && count($toArr) > 1) {
                foreach ($toArr as $key => $val) {
                    $rt = @mail($val, Strings::EncodeHeader(Strings::StripNewLines($m->Subject), 'text', $m->charset), $body, $header, $params);
                    
                    // implement call back function if it exists
                    $this->doCallback(($rt == 1) ? 1 : 0, $val, $m->cc, $m->bcc, $m->Subject, $body);
                }
            } 
            else {*/
            
                $rt = @mail($to, Strings::EncodeHeader(Strings::StripNewLines($m->Subject), 'text', $m->charset), $body, $header, $params);

                // implement call back function if it exists
                $this->doCallback(($rt == 1) ? 1 : 0, $to, $m->cc, $m->bcc, $m->Subject, $body);
            /*}*/
            
            @ini_set('sendmail_from', $old_from);
            
            if(!$rt)
                throw new MailException(MailErrorMessages::Instantiate, ExceptionTypes::StopCrytical);
            
            return true;
        }

        private function SmtpSend(MailMessage $m, $header, $body) {
            
            $bad_rcpt = array();
            
            if(!$this->SmtpConnect())
                throw new MailException(MailErrorMessages::SmtpConnectFailed, ExceptionTypes::StopCrytical);
            
            
            $smtp_from = ($this->sender == '') ? $m->from->address : $this->sender;
            if(!$this->smtp->Mail($smtp_from))
                throw new MailException(MailErrorMessages::FromFailed . $smtp_from, ExceptionTypes::StopCrytical);

            // Attempt to send attach all recipients
            foreach($m->to as $to) {
                if (!$this->smtp->Recipient($to->address)) {
                    $bad_rcpt[] = $to->address;
                    // implement call back function if it exists
                    $this->doCallback(0, $to->address, '', '', $m->Subject, $body);
                } else {
                    // implement call back function if it exists
                    $this->doCallback(1, $to->address, '', '', $this->Subject, $body);   
                }
            }
            
            foreach($m->cc as $cc) {
                if (!$this->smtp->Recipient($cc->address)) {
                    $bad_rcpt[] = $cc->formated;
                    // implement call back function if it exists
                    $this->doCallback(0, '', $cc->address, '', $m->Subject, $body);
                } else {
                    // implement call back function if it exists
                    $this->doCallback(1, '', $cc->address, '', $m->Subject, $body);
                }
            }
            
            foreach($m->bcc as $bcc) {
                if (!$this->smtp->Recipient($bcc->address)) {
                    $bad_rcpt[] = $bcc->formated;
                    // implement call back function if it exists
                    $this->doCallback(0, '', '', $bcc->address, $m->Subject, $body);
                } else {
                    // implement call back function if it exists
                    $this->doCallback(1, '', '', $bcc->address, $this->Subject, $body);
                }
            }


            if (count($bad_rcpt) > 0 )
                throw new MailException(MailErrorMessages::RecipientsFailed . implode(', ', $bad_rcpt));
            
            if(!$this->smtp->Data($header . $body))
                throw new MailException(MailErrorMessages::DataNotAccepted, ExceptionTypes::StopCrytical);

            if($this->SMTPKeepAlive == true)
                $this->smtp->Reset();

            return true;
        }

        private function SmtpConnect() {
            
            if(Variable::IsNull($this->smtp))
                $this->smtp = new SMTP();

            $this->smtp->do_debug = $this->SMTPDebug;
            $hosts = explode(';', $this->Host);
            $index = 0;
            $connection = $this->smtp->Connected();

            // Retry while there is no connection
            try {
                
                while($index < count($hosts) && !$connection) {
                    
                    $hostinfo = array();
                    
                    if (preg_match('/^(.+):([0-9]+)$/', $hosts[$index], $hostinfo)) {
                        $host = $hostinfo[1];
                        $port = $hostinfo[2];
                    } else {
                        $host = $hosts[$index];
                        $port = $this->Port;
                    }

                    $tls = ($this->SMTPSecure == 'tls');
                    $ssl = ($this->SMTPSecure == 'ssl');

                    if ($this->smtp->Connect(($ssl ? 'ssl://':'').$host, $port, $this->Timeout)) {

                        $hello = ($this->Helo != '' ? $this->Helo : $this->_hostName());
                        $this->smtp->Hello($hello);

                        if ($tls) {
                            if (!$this->smtp->StartTLS())
                                throw new MailException(MailErrorMessages::TSL);

                            //We must resend HELO after tls negotiation
                            $this->smtp->Hello($hello);
                        }

                        $connection = true;
                        if ($this->SMTPAuth)
                            if (!$this->smtp->Authenticate($this->Username, $this->Password))
                                throw new MailException(MailErrorMessages::Authenticate);
                                
                    }
                    
                    $index++;
                    if (!$connection)
                        throw new MailException(MailErrorMessages::ConnectHost);
                        
                }
                
            } 
            catch (MailException $e) {
                
                $this->smtp->Reset();
                throw $e;
                
            }
            
            return true;
            
        }

        private function SmtpClose() {
            if(!is_null($this->smtp)) {
                if($this->smtp->Connected()) {
                    $this->smtp->Quit();
                    $this->smtp->Close();
                }
            }
        }

        /**
        * Creates recipient headers.
        * @access public
        * @return string
        */
        private function _appendToHeader($type, $addr) {
            if($addr instanceOf MailAddress)
                return Strings::HeaderLine($type, $addr->formated);
            elseif($addr instanceOf MailAddressList)
                return Strings::HeaderLine($type, $addr->Join());
            else
                return Strings::HeaderLine($type, '');
        }

        

        /**
        * Set the body wrapping.
        * @access public
        * @return void
        */
        private function _setWordWrap($m) {
            if($m->wordwrap < 1)
                return;

            switch($m->type) {
                case 'alt':
                case 'alt_attachments':
                    $m->altbody = Strings::WrapText($m->altbody, $m->wordwrap, $m->charset);
                    break;
                default:
                    $m->body = Strings::WrapText($m->body, $m->wordwrap, $m->charset);
                    break;
            }
        }

        /**
        * Assembles message header.
        * @access public
        * @return string The assembled header
        */
        private function _createHeader(MailMessage $m) {
            $result = '';
             
            // Set the boundaries
            $uniq_id = md5(uniqid(time()));
            $this->boundary[1] = 'b1_' . $uniq_id;
            $this->boundary[2] = 'b2_' . $uniq_id;
             
            $result .= Strings::HeaderLine('Date', Date::RFC());
            if($m->ReturnPath != '') {
                $result .= Strings::HeaderLine('Return-Path', '<' . trim($m->ReturnPath) . '>');
            }
            else {
                if(Variable::IsNull($this->sender)) 
                    $result .= Strings::HeaderLine('Return-Path', trim($m->from->formated));
                else
                    $result .= Strings::HeaderLine('Return-Path', trim($this->sender->formated));
            }
            $result .= $this->_appendToHeader('From', $m->from);

            // To be created automatically by mail()
            if($this->Mailer != MailerTypes::Mail) {
                if($m->to->count > 0) {
                    $result .= $this->_appendToHeader('To', $m->to);
                } elseif($this->cc->count == 0) {
                    $result .= $this->_appendToHeader('To', 'undisclosed-recipients:;');
                }
            }
            
            // sendmail and mail() extract Cc from the header before sending
            if($m->cc->count > 0)
                $result .= $this->_appendToHeader('Cc', $m->cc);

            // sendmail and mail() extract Bcc from the header before sending
            if((($this->Mailer == MailerTypes::SendMail) || 
                ($this->Mailer == MailerTypes::Mail)) && 
                ($m->bcc->count > 0))
                $result .= $this->_appendToHeader('Bcc', $m->bcc);

            if($m->ReplyTo->count > 0)
                $result .= $this->_appendToHeader('Reply-to', $m->ReplyTo);
            // mail() sets the subject itself
            if($this->Mailer != MailerTypes::Mail)
              $result .= Strings::HeaderLine('Subject', Strings::EncodeHeader(Strings::StripNewLines($m->Subject), 'text', $m->charset));

            if($m->id != '')
                $result .= Strings::HeaderLine('Message-ID', '<'.$m->id.'@'.$this->_hostName().'>');
            else
                $result .= sprintf("Message-ID: <%s@%s>%s", $uniq_id, $this->_hostName(), Strings::LE);
            
            // $result .= Strings::HeaderLine('X-Priority', $m->Priority);
            // $result .= Strings::HeaderLine('X-Mailer', 'PHPMailer '.MailSender::Version.' (phpmailer.sourceforge.net)');

            if($m->ConfirmReadingTo != '') {
                $result .= Strings::HeaderLine('Disposition-Notification-To', '<' . trim($m->ConfirmReadingTo) . '>');
                $result .= Strings::HeaderLine('X-Confirm-Reading-To', '<' . trim($m->ConfirmReadingTo) . '>');
                $result .= Strings::HeaderLine('Return-Receipt-To', '<' . trim($m->ConfirmReadingTo) . '>');
            }

            // Add custom headers
            for($index = 0; $index < count($m->CustomHeader); $index++)
                $result .= Strings::HeaderLine(trim($m->CustomHeader[$index][0]), Strings::EncodeHeader(trim($m->CustomHeader[$index][1]), 'text', $m->charset));

                
            if (!$this->certificate) {
                $result .= Strings::HeaderLine('MIME-Version', '1.0');
                $result .= $this->_getMailMIME($m);
            }

            return $result;
        }

        /**
        * Returns the message MIME.
        * @access public
        * @return string
        */
        private function _getMailMIME($m) {
            
            $result = '';
            switch($m->type) {
                case 'plain':
                    $result .= Strings::HeaderLine('Content-Transfer-Encoding', $m->Encoding);
                    $result .= sprintf("Content-Type: %s; charset=\"%s\"", $m->ContentType, $m->CharSet);
                    break;
                case 'attachments':
                case 'alt_attachments':
                    if($m->attachments->HasInline())
                        $result .= sprintf("Content-Type: %s;%s\ttype=\"text/html\";%s\tboundary=\"%s\"%s", 'multipart/related', Strings::LE, Strings::LE, $this->boundary[1], Strings::LE);
                    else {
                        $result .= Strings::HeaderLine('Content-Type', 'multipart/mixed;');
                        $result .= Strings::TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                    }
                    break;
                case 'alt':
                    $result .= Strings::HeaderLine('Content-Type', 'multipart/alternative;');
                    $result .= Strings::TextLine("\tboundary=\"" . $this->boundary[1] . '"');
                    break;
            }

            if($this->Mailer != MailerTypes::Mail)
                $result .= Strings::LE.Strings::LE;

            return $result;
        }

        /**
        * Assembles the message body.  Returns an empty string on failure.
        * @access public
        * @return string The assembled message body
        */
        private function _createBody(MailMessage $m) {
            $body = '';

            if ($this->certificate)
                $body .= $this->_getMailMIME();

            $this->_setWordWrap($m);

            try {
                switch($m->type) {
                    case 'alt':
                        $body .= Strings::GetBoundaryBegin($this->boundary[1], '', 'text/plain', '');
                        $body .= Strings::EncodeString($m->AltBody, $m->Encoding);
                        $body .= Strings::LE.Strings::LE;
                        $body .= Strings::GetBoundaryBegin($this->boundary[1], '', 'text/html', '');
                        $body .= Strings::EncodeString($m->Body, $m->Encoding);
                        $body .= Strings::LE.Strings::LE;
                        $body .= Strings::GetBoundaryEnd($this->boundary[1]);
                        break;
                    case 'plain':
                        $body .= Strings::EncodeString($m->body, $m->encoding);
                        break;
                    case 'attachments':
                        $body .= Strings::GetBoundaryBegin($this->boundary[1], $m->Charset, 'text/html', $m->Encoding);
                        $body .= Strings::EncodeString($m->Body, $m->Encoding);
                        $body .= Strings::LE;
                        $body .= $this->AttachAll($m);
                        break;
                    case 'alt_attachments':
                        $body .= sprintf("--%s%s", $this->boundary[1], Strings::LE);
                        $body .= sprintf("Content-Type: %s;%s" . "\tboundary=\"%s\"%s", 'multipart/alternative', Strings::LE, $this->boundary[2], Strings::LE.Strings::LE);
                        $body .= Strings::GetBoundaryBegin($this->boundary[2], '', 'text/plain', '') . Strings::LE; // Create text body
                        $body .= Strings::EncodeString($m->AltBody, $m->Encoding);
                        $body .= Strings::LE.Strings::LE;
                        $body .= Strings::GetBoundaryBegin($this->boundary[2], '', 'text/html', '') . Strings::LE; // Create the HTML body
                        $body .= Strings::EncodeString($m->Body, $m->Encoding);
                        $body .= Strings::LE.Strings::LE;
                        $body .= Strings::GetBoundaryEnd($this->boundary[2]);
                        $body .= $this->AttachAll();
                        break;
                }
            }
            catch(MailException $e) {
                $body = '';
            }
            
            if($body != '' && $this->certificate) {
                try {
                    $file = tempnam('', 'mail');
                    file_put_contents($file, $body); //TODO check this worked
                    $signed = tempnam("", "signed");
                    if (@openssl_pkcs7_sign($file, $signed, "file://".$this->certificate->file, array("file://".$this->certificate->key, $this->certificate->pass), null)) {
                        @unlink($file);
                        @unlink($signed);
                        $body = file_get_contents($signed);
                    } else {
                        @unlink($file);
                        @unlink($signed);
                        throw new MailException(MailErrorMessages::Signing.openssl_error_string());
                    }
                } catch (MailException $e) {
                    $body = '';
                    throw $e;
                }
            }

            return $body;
        }

        
        
        
        /**
        * Attaches all fs, string, and binary attachments to the message.
        * Returns an empty string on failure.
        * @access private
        * @return string
        */
        private function AttachAll(MailMessage $m) {
            // Return text of body
            $mime = array();
            $cidUniq = array();
            $incl = array();

            // Add all attachments
            foreach ($m->attachments as $attachment) {
                
                //$attachment = new MailAttachment();
                
                // Check for string attachment
                if ($attachment->isString)
                    $string = $attachment->path;
                else
                    $path = $attachment->path;
                

                if (in_array($attachment->path, $incl)) 
                    continue; 
                
                $filename    = $attachment->filename;
                $name        = $attachment->name;
                $encoding    = $attachment->encoding;
                $type        = $attachment->type;
                $disposition = $attachment->isInline ? 'inline' : 'attachment';
                $cid         = $attachment->cid;
                $incl[]      = $attachment->path;
                
                if ( $disposition == 'inline' && isset($cidUniq[$cid]) ) 
                    continue;
                    
                $cidUniq[$cid] = true;

                $mime[] = sprintf("--%s%s", $this->boundary[1], Strings::LE);
                $mime[] = sprintf("Content-Type: %s; name=\"%s\"%s%s", $type, Strings::EncodeHeader(Strings::StripNewLines($name), 'text', $m->charset), ($attachment->charset ? '; charset='.$attachment->charset : '') ,Strings::LE);
                $mime[] = sprintf("Content-Transfer-Encoding: %s%s", $encoding, Strings::LE);
                
                if($disposition == 'inline')
                    $mime[] = sprintf("Content-ID: <%s>%s", $cid, Strings::LE);

                $mime[] = sprintf("Content-Disposition: %s; filename=\"%s\"%s", $disposition, Strings::EncodeHeader(Strings::StripNewLines($name), 'text', $m->charset), Strings::LE.Strings::LE);

                // Encode as string attachment
                if($attachment->isString) {
                    $mime[] = Strings::EncodeString($string, $encoding);
                    $mime[] = Strings::LE.Strings::LE;
                } else {
                    $mime[] = Strings::EncodeFile($path, $encoding);
                    $mime[] = Strings::LE.Strings::LE;
                }
            }

            $mime[] = sprintf("--%s--%s", $this->boundary[1], Strings::LE);
            
            return join('', $mime);
        }

        /**
        * Returns the server hostname or 'localhost.localdomain' if unknown.
        * @access private
        * @return string
        */
        private function _hostName() {
            
            if($this->DOMAIN != '')
                $result = $this->DOMAIN;
            else {
                if (isset($_SERVER['SERVER_NAME']))
                    $result = $_SERVER['SERVER_NAME'];
                else
                    $result = 'localhost.localdomain';
            }
            return $result;
        }

        
        protected function doCallback($isSent,$to,$cc,$bcc,$subject,$body) {
            if (!empty($this->callback) && CodeKit::Exists($this->callbackcallback)) {
              $params = array($isSent,$to,$cc,$bcc,$subject,$body);
              call_user_func_array($this->callback,$params);
            }
        }
    }
    
    class MailTemplate {
        
        private $_data;
        
        public function __construct($file, $isFile = true) {
            // $this->_data = $isFile ? FileInfo::ReadAll($file) : $file;
            if($isFile) {
                ob_start();
                require($file);
                $this->_data = ob_get_contents();
                ob_end_clean();
            }
            else
                $this->_data = $file;

        }
        
        public static function Create($file, $isFile = true) {
            return new MailTemplate($file, $isFile);
        }
        
        public static function ObjectToReplacements($object, $startKey = 'item') {
            $replacements = array();
            foreach($object as $key => $value) {
                if(is_object($value)) {
                    $replacements = array_merge($replacements, MailTemplate::ObjectToReplacements($value, $startKey.'.'.$key));
                }
                else if(is_array($value)) {
                    foreach($value as $index => $vvv) {
                        $replacements[':'.$startKey.'['.$index.']:'] = $vvv;
                    }
                }
                else
                    $replacements[':'.$startKey.'.'.$key.':'] = $value;
            }
            return $replacements;
        }
        
        public function Apply($replacements) {
            foreach($replacements as $key => $value) {
                $this->_data = str_replace($key, $value, $this->_data);
            }
            if ($this->_data) {
                $xml = XMLNode::LoadHTML('<'.'?xml version="1.0" encoding="utf-8" ?'.'>'.$this->_data, false, 'utf-8');
                foreach($xml->Query('//img') as $image) 
                    if($image->attributes->src && strstr($image->attributes->src->value, '://') === false)
                        $image->attributes->src->value = 'https://'.Request::$i->server->server_name.$image->attributes->src->value;       
                
                foreach($xml->Query('//iframe') as $iframe) 
                    if($iframe->attributes->src && strstr($iframe->attributes->src->value, '://') === false)
                        $iframe->attributes->src->value = 'https://'.Request::$i->server->server_name.$iframe->attributes->src->value;       

                foreach($xml->Query('//a') as $a)                 
                    if($a->attributes->href && strstr($a->attributes->href->value, '://') === false)
                        $a->attributes->href->value = 'https://'.Request::$i->server->server_name.$a->attributes->href->value;                
                
                $this->_data = $xml->body->html;
                $this->_data = str_replace('<body>', '', str_replace('</body>', '', $this->_data));
            }
            return $this;
        }
        
        public function ToString($replacements = false) {
            if($replacements)
                $this->Apply($replacements);
            return $this->_data;
        }
        
    }
    
    class MailQueue {
        
        public static function Send(MailSender $sender, $mail) {
            
            $xml = '<'.'?xml version="1.0" encoding="utf-8"?'.'><sendmail>';
            $xml .= '<sender>'.Variable::Bin2Hex(serialize($sender)).'</sender>';
            $xml .= '<message>'.Variable::Bin2Hex(serialize($mail)).'</message>';
            $xml .= '</sendmail>';
            
            $md5 = md5($xml);
            
            FileInfo::WriteAll(_CACHE.'sendmail/'.microtime(true).'.'.$md5.'.mail', $xml, true, 0777);

        }
        
        public static function CronProcess() {
            
            $df = new DirectoryFinder();
            $files = $df->Files(_CACHE.'sendmail/');
            $iFile = 0;
            out('starting ... ', MAIL_QUEUE_SEND_PER_TIME);
            foreach($files as $file) {
                
                if($iFile >= MAIL_QUEUE_SEND_PER_TIME) 
                    break;
                    
                out('file', _CACHE.'sendmail/'.$file->name);
                
                $content = FileInfo::ReadAll(_CACHE.'sendmail/'.$file->name);
                if ( ! $content)
                    continue;
                $xml = XMLNode::Load($content, false);

                $sender = $xml->Query('//sender');
                if($sender->count == 0)
                    continue;
                    
                $sender = unserialize(Variable::Hex2Bin($sender->first->value));
                
                $mail = $xml->Query('//message')->first;
                if ($mail) {
                    $mail = unserialize(Variable::Hex2Bin($mail->value));
                    if(!$mail)
                        continue;
                        
                    try {
                        if (FileInfo::Exists(_CACHE.'sendmail/'.$file->name)) {
                            FileInfo::Delete(_CACHE.'sendmail/'.$file->name);
                            $sender->Send($mail);
                            out('sended!');
                        }
                    }
                    catch(Exception $e) {
                        out($e->getMessage());
                        FileInfo::WriteAll(_CACHE.'sendmail/errors/'.$file->name, $content, true, 0777);
                    }
                    
                    $iFile++;
                }

            }
            
            out('end ... ', $iFile);
            
        }
                                                             
    }

?>
