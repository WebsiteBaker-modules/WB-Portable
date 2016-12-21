<?php
/**
 *
 * @category        framework
 * @package         frontend
 * @subpackage      wbmailer
 * @author          Ryan Djurovich, WebsiteBaker Project
 * @copyright       WebsiteBaker Org. e.V.
 * @link            http://websitebaker.org/
 * @license         http://www.gnu.org/licenses/gpl.html
 * @platform        WebsiteBaker 2.8.3
 * @requirements    PHP 5.3.6 and higher
 * @version         $Id: class.wbmailer.php 1499 2012-02-29 01:50:57 +0100 DarkViper $
 * @filesource      $HeadURL: svn://isteam.dynxs.de/wb_svn/wb280/branches/2.8.x/wb/framework/class.wbmailer.php $
 * @lastmodified    $Date: 2012-02-29 01:50:57 +0100 (Mi, 29. Feb 2012) $
 * @examples        http://phpmailer.worxware.com/index.php?pg=examples
 *
 */
/* -------------------------------------------------------- */
// Must include code to stop this file being accessed directly
if(!defined('WB_PATH')) {
    require_once(dirname(__FILE__).'/globalExceptionHandler.php');
    throw new IllegalFileException();
}
/* -------------------------------------------------------- */
//SMTP needs accurate times, and the PHP time zone MUST be set
//This should be done in your php.ini, but this is how to do it if you don't have access to that
date_default_timezone_set('Etc/UTC');

// Include PHPMailer autoloader in initialize

class wbmailer extends PHPMailer
{
    // new websitebaker mailer class (subset of PHPMailer class)
    // setting default values

    function __construct($exceptions = false) {//

        parent::__construct($exceptions);//
        $database = $GLOBALS['database'];
        // set mailer defaults (PHP mail function)
        $db_wbmailer_routine = "phpmail";
        $db_wbmailer_smtp_host = "";
        $db_wbmailer_smtp_port = 25;
        $db_wbmailer_smtp_secure = '';
        $db_wbmailer_default_sendername = 'WB Mailer';
        $db_server_email = '';

        // get mailer settings from database
        $sql = 'SELECT * FROM `' .TABLE_PREFIX. 'settings` '
              . 'WHERE `name` LIKE (\'wbmailer\_%\') '
              . 'OR `name`=\'server_email\'';
        $oRes = $database->query($sql);
        while($aSettings = $oRes->fetchRow( MYSQLI_ASSOC )) {
            switch ($aSettings['name']):
                case 'wbmailer_routine':
                case 'wbmailer_smtp_host':
                case 'wbmailer_smtp_port':
                case 'wbmailer_smtp_secure':
                case 'wbmailer_smtp_username':
                case 'wbmailer_smtp_password':
                case 'wbmailer_default_sendername':
                case 'server_email':
                    ${'db_'.$aSettings['name']} = $aSettings['value'];
                    break;
                case 'wbmailer_smtp_auth':
                    ${'db_'.$aSettings['name']} =  ( ($aSettings['value']=='true') || (intval($aSettings['value'])==1) ?true:false );
                    break;
                default:
                break;
            endswitch;
        }

/**
     * `echo` Output plain-text as-is, appropriate for CLI
     * `html` Output escaped, line breaks converted to `<br>`, appropriate for browser output
     * `error_log` Output to error log as configured in php.ini
     *
     * Alternatively, you can provide a callable expecting two params: a message string and the debug level:
     * <code>
     * $this->Debugoutput = function($str, $level) {echo "debug level $level; message: $str";};
     * </code>
 */

        $this->set('SMTPDebug', ((defined('DEBUG') && DEBUG)?4:0));    // Enable verbose debug output
        $this->set('Debugoutput', 'error_log');

        // set method to send out emails
        if ($db_wbmailer_routine == "smtp" && mb_strlen($db_wbmailer_smtp_host) > 5 ) {
            // use SMTP for all outgoing mails send by Website Baker
            $this->isSMTP();                                               // telling the class to use SMTP
            $this->set('SMTPAuth', false);                                 // enable SMTP authentication
            $this->set('Host', $db_wbmailer_smtp_host);                    // Set the hostname of the mail server
            $this->set('Port', intval($db_wbmailer_smtp_port));            // Set the SMTP port number - likely to be 25, 465 or 587
            $this->set('SMTPSecure', strtolower($db_wbmailer_smtp_secure));// Set the encryption system to use - ssl (deprecated) or tls
            $this->set('SMTPKeepAlive', false);                            // SMTP connection will be close after each email sent
            // check if SMTP authentification is required
            if ($db_wbmailer_smtp_auth  && (mb_strlen($db_wbmailer_smtp_username) > 1) && (mb_strlen($db_wbmailer_smtp_password) > 1) ) {
                // use SMTP authentification
                $this->set('SMTPAuth', true);                                                 // enable SMTP authentication
                $this->set('Username',   $db_wbmailer_smtp_username);                         // set SMTP username
                $this->set('Password',   $db_wbmailer_smtp_password);                         // set SMTP password
            }
        } else if ($db_wbmailer_routine == "phpmail" && strlen($db_wbmailer_smtp_host) > 5 ) {
            // use PHP mail() function for outgoing mails send by Website Baker
            $this->IsMail();
        } else {
            $this->isSendmail();   // telling the class to use SendMail transport
        }

        // set language file for PHPMailer error messages
        if(defined("LANGUAGE")) {
            $this->SetLanguage(strtolower(LANGUAGE),"language");    // english default (also used if file is missing)
        }

        // set default charset
        if(defined('DEFAULT_CHARSET')) {
            $this->set('CharSet', DEFAULT_CHARSET);
        } else {
            $this->set('CharSet', 'utf-8');
        }

        // set default sender name
        if($this->FromName == 'Root User') {
            if(isset($_SESSION['DISPLAY_NAME'])) {
                $this->set('FromName', $_SESSION['DISPLAY_NAME']);                  // FROM NAME: display name of user logged in
            } else {
                $this->set('FromName', $db_wbmailer_default_sendername);            // FROM NAME: set default name
            }
        }

        /*
            some mail provider (lets say mail.com) reject mails send out by foreign mail
            relays but using the providers domain in the from mail address (e.g. myname@mail.com)
        $this->setFrom($db_server_email);                       // FROM MAIL: (server mail)
        */

        // set default mail formats
        $this->IsHTML();                                        // Sets message type to HTML or plain.
        $this->set('WordWrap', 80);
        $this->set('Timeout', 30);
    }

    /**
     * Send messages using $Sendmail.
     * @return void
     * @description  overrides isSendmail() in parent
     */
    public function isSendmail()
    {
        $ini_sendmail_path = ini_get('sendmail_path');
        if (!preg_match('/sendmail$/i', $ini_sendmail_path)) {
            if ($this->exceptions) {
                throw new phpmailerException('no sendmail available');
            }
        } else {
            $this->Sendmail = $ini_sendmail_path;
            $this->Mailer = 'sendmail';
        }
    }

}
