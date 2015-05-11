<?php

/**
 * Helper class for imap access
 *
 * @package    protocols
 * @copyright  Copyright (c) Tobias Zeising (http://www.aditu.de)
 * @license    GPLv3 (http://www.gnu.org/licenses/gpl-3.0.html)
 * @author     Tobias Zeising <tobias.zeising@aditu.de>
 */

namespace FinFlow\Drivers;

use FinFlow\CanValidate;

class EmailFetchAgent {

	const IMAP = 'imap';
	const POP3 = 'pop3';

	const SSL = 'ssl';
	const TLS = 'tls';

    /**
     * connection
     */
    private $connection = false;
    
    /**
     * mailbox url string
     */
    private $mailbox = "";
    
    /**
     * currentfolder
     */
    private $folder = "Inbox";

	/**
	 * errors array
	 * @var array
	 */
    protected $errors = array();


	/**
	 * initialize imap helper
	 * @param string $protocol
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param bool $encryption
	 * @param null $port
	 * @param int $timeout
	 * @param bool $validate_cert
	 *
	 * @throws EmailAgent_Exception
	 */
    public function __construct($protocol='imap', $host='localhost', $username='anonymous', $password='', $encryption = false, $port = null, $timeout=10, $validate_cert=true) {

	    $protocol = strtolower($protocol);
	    $timeout  = intval($timeout);

	    $port  = empty($port) ? ( $protocol == self::IMAP ? ( $encryption ? 993 : 143  ) : ( $encryption ? 995 : 110  ) ) : intval($port);
		$enc   = $encryption ? ( $validate_cert ? ( '/' . $encryption ) : ( '/novalidate-cert/' . $encryption ) ) : '';

	    //check for valid protocol
	    if( ( self::IMAP != $protocol ) and ( self::POP3 != $protocol ) )
		    throw new EmailAgent_Exception(
			    __t('Protocol <em>%s</em> is not supported!', $protocol),
			    EmailAgent_Exception::ERR_PROTOCOL_NOT_SUPPORTED
		    );


	    //check if valid host
	    if( ! CanValidate::hostname($host) )
		    throw new EmailAgent_Exception(
			    __t('Invalid host <em>%s</em>! Enter a valid ip address or hostname and try again', $host),
			    EmailAgent_Exception::ERR_INVALID_HOST
		    );

	    //build mailbox string
	    $mailbox = ( $host . ':' . $port . '/' . $protocol . $enc );

	    //set timeout
	    $this->setTimeout($timeout);

	    //open the imap connection
	    $this->connect($mailbox, $username, $password);

	    if( ! $this->connection )
		    throw new EmailAgent_Exception(
			    __t('Could not connect to host <em>%1s</em> via port %2s. Check your connection settings and try again.', $host, $port),
			    EmailAgent_Exception::ERR_CONNECTION_FAILED
		    );


    }

	public function setTimeout($timeout){

		imap_timeout(IMAP_OPENTIMEOUT, $timeout);
		imap_timeout(IMAP_READTIMEOUT, $timeout);
		imap_timeout(IMAP_WRITETIMEOUT, $timeout);
		imap_timeout(IMAP_CLOSETIMEOUT, $timeout);

	}

	public function connect($mailbox, $username, $password=''){

		$this->mailbox    = ( "{" . $mailbox . "}" );
		$this->connection = @imap_open($this->mailbox, $username, $password);

	}

    
    /**
     * close connection
     */
    function __destruct() {
        if( $this->connection !== false ){
	        imap_close($this->connection);
        }
    }
   
   
    /**
     * returns true after successfull connection
     *
     * @return bool true on success
     */
    public function isConnected() {
        return $this->connection !== false;
    }
    
    
    /**
     * returns last imap error
     *
     * @return string error message
     */
    public function getError() {
        return imap_last_error();
    }

	public function getErrors(){
		return imap_errors();
	}
    
    
    /**
     * select given folder
     *
     * @return bool successfull opened folder
     * @param $folder name
     */
    public function selectFolder($folder) {
        $result = imap_reopen($this->connection, $this->mailbox . $folder);
        if($result === true)
            $this->folder = $folder;
        return $result;
    }
    
    
    /**
     * returns all available folders
     *
     * @return array with foldernames
     */
    public function getFolders() {
        $folders = imap_list($this->connection, $this->mailbox, "*");
        return str_replace($this->mailbox, "", $folders);
    }
    
    
    /**
     * returns the number of messages in the current folder
     *
     * @return int message count
     */
    public function countMessages() {
        return imap_num_msg($this->connection);
    }
    
    
    /**
     * returns the number of unread messages in the current folder
     *
     * @return int message count
     */
    public function countUnreadMessages() {
        $result = imap_search($this->connection, 'UNSEEN');
        if($result===false)
            return false;
        return count($result);
    }

    /**
     * returns unseen emails in the current folder
     *
     * @return array messages
     * @param $withbody without body
     */
    public function getUnreadMessages($withbody=true){
        $result = imap_search($this->connection, 'UNSEEN');
        foreach($result as $k=>$i){
            $emails[]= $this->formatMessage($i, $withbody);
        }
        return $emails;
    }
    
    
    /**
     * returns all emails in the current folder
     *
     * @return array messages
     * @param $withbody without body
     */
    public function getMessages($withbody = true) {
        $count = $this->countMessages();
        $emails = array();
        for($i=1;$i<=$count;$i++) {
            $emails[]= $this->formatMessage($i, $withbody);
        }

        // sort emails descending by date
        // usort($emails, function($a, $b) {
        // try {
        // $datea = new \DateTime($a['date']);
        // $dateb = new \DateTime($b['date']);
        // } catch(\Exception $e) {
        // return 0;
        // }
        // if ($datea == $dateb)
        // return 0;
        // return $datea < $dateb ? 1 : -1;
        // });

        return $emails;
    }
    
    /**
     * @param $id
     * @param bool $withbody
     * @return array
     */
    protected function formatMessage($id, $withbody=true){
        $header = imap_headerinfo($this->connection, $id);

        // fetch unique uid
        $uid = imap_uid($this->connection, $id);

        // get email data
        if ( isset($header->subject) && strlen($header->subject) > 0 ) {
            $subject = imap_mime_header_decode($header->subject);
            $subject = $subject[0]->text;
        } else {
            $subject = '';
        }
        $subject = $this->convertToUtf8($subject);
        $email = array(
            'to'       => isset($header->to) ? $this->arrayToAddress($header->to) : '',
            'from'     => $this->toAddress($header->from[0]),
            'date'     => $header->date,
            'subject'  => $subject,
            'uid'       => $uid,
            'unread'   => strlen(trim($header->Unseen))>0,
            'answered' => strlen(trim($header->Answered))>0
        );
        if(isset($header->cc))
            $email['cc'] = $this->arrayToAddress($header->cc);

        // get email body
        if($withbody===true) {
            $body = $this->getBody($uid);
            $email['body'] = $body['body'];
            $email['html'] = $body['html'];
        }

        // get attachments
        $mailStruct = imap_fetchstructure($this->connection, $id);
        $attachments= $this->attachments2name($this->getAttachments($this->connection, $id, $mailStruct, ""));

        if( count($attachments) > 0 )
            $email['attachments'] = $attachments;

        return $email;
    }
    
    /**
     * delete given message
     *
     * @return bool success or not
     * @param $id of the message
     */
    public function deleteMessage($id) {
        return $this->deleteMessages(array($id));
    }
    
    
    /**
     * delete messages
     *
     * @return bool success or not
     * @param $ids array of ids
     */
    public function deleteMessages($ids) {
        if( imap_mail_move($this->connection, implode(",", $ids), $this->getTrash(), CP_UID) == false)
                return false;
        return imap_expunge($this->connection);
    }
    
    
    /**
     * move given message in new folder
     *
     * @return bool success or not
     * @param $id of the message
     * @param $target new folder
     */
    public function moveMessage($id, $target) {
        return $this->moveMessages(array($id), $target);
    }
    
    
    /**
     * move given message in new folder
     *
     * @return bool success or not
     * @param $ids array of message ids
     * @param $target new folder
     */
    public function moveMessages($ids, $target) {
        if(imap_mail_move($this->connection, implode(",", $ids), $target, CP_UID)===false)
            return false;
        return imap_expunge($this->connection);
    }
    
    
    /**
     * mark message as read
     *
     * @return bool success or not
     * @param $id of the message
     * @param $seen true = message is read, false = message is unread
     */
    public function setUnseenMessage($id, $seen = true) {
        $header = $this->getMessageHeader($id);
        if($header==false)
            return false;
            
        $flags = "";
        $flags .= (strlen(trim($header->Answered))>0 ? "\\Answered " : '');
        $flags .= (strlen(trim($header->Flagged))>0 ? "\\Flagged " : '');
        $flags .= (strlen(trim($header->Deleted))>0 ? "\\Deleted " : '');
        $flags .= (strlen(trim($header->Draft))>0 ? "\\Draft " : '');
        
        $flags .= (($seen == true) ? '\\Seen ' : ' ');
        echo "\n<br />".$id.": ".$flags;
        imap_clearflag_full($this->connection, $id, '\\Seen', ST_UID);
        return imap_setflag_full($this->imap, $id, trim($flags), ST_UID);
    }
    
    
    /**
     * return content of messages attachment
     *
     * @return binary attachment
     * @param $id of the message
     * @param $index of the attachment (default: first attachment)
     */
    public function getAttachment($id, $index = 0) {

        // find message
        $attachments    = false;

        $messageIndex   = imap_msgno($this->connection, $id);
        $header         = imap_headerinfo($this->connection, $messageIndex);
        $mailStruct     = imap_fetchstructure($this->connection, $messageIndex);
        $attachments    = $this->getAttachments($this->connection, $messageIndex, $mailStruct, "");
        
        if( $attachments == false )
            return false;
        
        // find attachment
        if( $index > count($attachments ))
            return false;

        $attachment = $attachments[$index];
        
        // get attachment body
        $partStruct = imap_bodystruct($this->connection, imap_msgno($this->connection, $id), $attachment['partNum']);
        $filename   = $partStruct->dparameters[0]->value;
        $message    = imap_fetchbody($this->connection, $id, $attachment['partNum'], FT_UID);
     
        switch ( $attachment['enc'] ) {
            case 0:
            case 1:
                $message = imap_8bit($message);
                break;
            case 2:
                $message = imap_binary($message);
                break;
            case 3:
                $message = imap_base64($message);
                break;
            case 4:
                $message = quoted_printable_decode($message);
                break;
        }
     
        return array(
                "name" => $attachment['name'], 
                "size" => $attachment['size'],
                "content" => $message);
    }
    
    
    /**
     * add new folder
     *
     * @return bool success or not
     * @param $name of the folder
     */
    public function addFolder($name) {
        return imap_createmailbox($this->connection , $this->mailbox . $name);
    }
    
    
    /**
     * remove folder
     *
     * @return bool success or not
     * @param $name of the folder
     */
    public function removeFolder($name) {
        return imap_deletemailbox($this->connection, $this->mailbox . $name);
    }
    
    
    /**
     * rename folder
     *
     * @return bool success or not
     * @param $name of the folder
     * @param $newname of the folder
     */
    public function renameFolder($name, $newname) {
        return imap_renamemailbox($this->connection, $this->mailbox . $name, $this->mailbox . $newname);
    }
    
    
    /**
     * clean folder content of selected folder
     *
     * @return bool success or not
     */
    public function purge() {
        // delete trash and spam
        if($this->folder==$this->getTrash() || strtolower($this->folder)=="spam") {
            if(imap_delete($this->connection,'1:*')===false) {
                return false;
            }
            return imap_expunge($this->connection);
        
        // move others to trash
        } else {
            if( imap_mail_move($this->connection,'1:*', $this->getTrash()) == false)
                return false;
            return imap_expunge($this->connection);
        }
    }
    
    
    /**
     * returns all email addresses
     *
     * @return array with all email addresses or false on error
     */
    public function getAllEmailAddresses() {
        $saveCurrentFolder = $this->folder;
        $emails = array();
        foreach($this->getFolders() as $folder) {
            $this->selectFolder($folder);
            foreach($this->getMessages(false) as $message) {
                $emails[] = $message['from'];
                $emails = array_merge($emails, $message['to']);
                if(isset($message['cc']))
                    $emails = array_merge($emails, $message['cc']);
            }
        }
        $this->selectFolder($saveCurrentFolder);
        return array_unique($emails);
    }
    
    
    /**
     * save email in sent
     *
     * @return void
     * @param $header
     * @param $body
     */
    public function saveMessageInSent($header, $body) {
        return imap_append($this->connection, $this->mailbox . $this->getSent(), $header . "\r\n" . $body . "\r\n", "\\Seen");
    }
    
    
    
    // private helpers
    
    
    /**
     * get trash folder name or create new trash folder
     *
     * @return trash folder name
     */
    private function getTrash() {
        foreach($this->getFolders() as $folder) {
            if(strtolower($folder)==="trash" || strtolower($folder)==="papierkorb")
                return $folder;
        }
        
        // no trash folder found? create one
        $this->addFolder('Trash');
        
        return 'Trash';
    }
    
    
    /**
     * get sent folder name or create new sent folder
     *
     * @return sent folder name
     */
    private function getSent() {
        foreach($this->getFolders() as $folder) {
            if(strtolower($folder)==="sent" || strtolower($folder)==="gesendet")
                return $folder;
        }
        
        // no sent folder found? create one
        $this->addFolder('Sent');
        
        return 'Sent';
    }
    
    
    /**
     * fetch message by id
     *
     * @return header
     * @param $id of the message
     */
    private function getMessageHeader($id) {
        $count = $this->countMessages();
        for($i=1;$i<=$count;$i++) {
            $uid = imap_uid($this->connection, $i);
            if($uid==$id) {
                $header = imap_headerinfo($this->connection, $i);
                return $header;
            }
        }
        return false;
    }
    
    
    /**
     * convert attachment in array(name => ..., size => ...).
     *
     * @return array
     * @param $attachments with name and size
     */
    private function attachments2name($attachments) {
        $names = array();
        foreach($attachments as $attachment) {
            $names[] = array(
                'name' => $attachment['name'],
                'size' => $attachment['size']
            );
        }
        return $names;
    }
    
    
    /**
     * convert imap given address in string
     *
     * @return string in format "Name <email@bla.de>"
     * @param $headerinfos the infos given by imap
     */
    private function toAddress($headerinfos) {
        $email = "";
        $name = "";
        if(isset($headerinfos->mailbox) && isset($headerinfos->host)) {
            $email = $headerinfos->mailbox . "@" . $headerinfos->host;
        }

        if(!empty($headerinfos->personal)) {
            $name = imap_mime_header_decode($headerinfos->personal);
            $name = $name[0]->text;
        } else {
            $name = $email;
        }
        
        $name = $this->convertToUtf8($name);
        
        return $name . " <" . $email . ">";
    }

    
    /**
     * converts imap given array of addresses in strings
     *
     * @return array with strings (e.g. ["Name <email@bla.de>", "Name2 <email2@bla.de>"]
     * @param $addresses imap given addresses as array
     */
    private function arrayToAddress($addresses) {
        $addressesAsString = array();
        foreach($addresses as $address) {
            $addressesAsString[] = $this->toAddress($address);
        }
        return $addressesAsString;
    }

    
    /**
     * returns body of the email. First search for html version of the email, then the plain part.
     *
     * @return string email body
     * @param $uid message id
     */
    private function getBody($uid) {
        $body = $this->get_part($this->connection, $uid, "TEXT/HTML");
        $html = true;
        // if HTML body is empty, try getting text body
        if ($body == "") {
            $body = $this->get_part($this->connection, $uid, "TEXT/PLAIN");
            $html = false;
        }
        $body = $this->convertToUtf8($body);
        return array( 'body' => $body, 'html' => $html);
    }
    
    
    /**
     * convert to utf8 if necessary.
     *
     * @return true or false
     * @param $string utf8 encoded string
     */
    function convertToUtf8($str) { 
        if(mb_detect_encoding($str, "UTF-8, ISO-8859-1, GBK")!="UTF-8")
            $str = utf8_encode($str);
        $str = iconv('UTF-8', 'UTF-8//IGNORE', $str);
        return $str; 
    } 
    
    
    /**
     * returns a part with a given mimetype
     * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
     *
     * @return string email body
     * @param $imap imap stream
     * @param $uid message id
     * @param $mimetype
     */
    private function get_part($imap, $uid, $mimetype, $structure = false, $partNumber = false) {
        if (!$structure) {
               $structure = imap_fetchstructure($imap, $uid, FT_UID);
        }
        if ($structure) {
            if ($mimetype == $this->get_mime_type($structure)) {
                if (!$partNumber) {
                    $partNumber = 1;
                }
                $text = imap_fetchbody($imap, $uid, $partNumber, FT_UID | FT_PEEK);
                switch ($structure->encoding) {
                    case 3: return imap_base64($text);
                    case 4: return imap_qprint($text);
                    default: return $text;
               }
           }
     
            // multipart 
            if ($structure->type == 1) {
                foreach ($structure->parts as $index => $subStruct) {
                    $prefix = "";
                    if ($partNumber) {
                        $prefix = $partNumber . ".";
                    }
                    $data = $this->get_part($imap, $uid, $mimetype, $subStruct, $prefix . ($index + 1));
                    if ($data) {
                        return $data;
                    }
                }
            }
        }
        return false;
    }
    
    
    /**
     * extract mimetype
     * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
     *
     * @return string mimetype
     * @param $structure
     */
    private function get_mime_type($structure) {
        $primaryMimetype = array("TEXT", "MULTIPART", "MESSAGE", "APPLICATION", "AUDIO", "IMAGE", "VIDEO", "OTHER");
     
        if ($structure->subtype) {
           return $primaryMimetype[(int)$structure->type] . "/" . $structure->subtype;
        }
        return "TEXT/PLAIN";
    }
    
    
    /**
     * get attachments of given email
     * taken from http://www.sitepoint.com/exploring-phps-imap-library-2/
     *
     * @return array of attachments
     * @param $imap stream
     * @param $mailNum email
     * @param $part
     * @param $partNum
     */
    private function getAttachments($imap, $mailNum, $part, $partNum) {
        $attachments = array();
     
        if (isset($part->parts)) {
            foreach ($part->parts as $key => $subpart) {
                if($partNum != "") {
                    $newPartNum = $partNum . "." . ($key + 1);
                }
                else {
                    $newPartNum = ($key+1);
                }
                $result = $this->getAttachments($imap, $mailNum, $subpart,
                    $newPartNum);
                if (count($result) != 0) {
                     array_push($attachments, $result);
                 }
            }
        }
        else if (isset($part->disposition)) {
            if (strtolower($part->disposition) == "attachment") {
                $partStruct = imap_bodystruct($imap, $mailNum,
                    $partNum);
                $attachmentDetails = array(
                    "name"    => $part->dparameters[0]->value,
                    "partNum" => $partNum,
                    "enc"     => $partStruct->encoding,
                    "size"    => $part->bytes
                );
                return $attachmentDetails;
            }
        }
     
        return $attachments;
    }
    
}

?>
