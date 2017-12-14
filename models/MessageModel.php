<?php

class MessageModel extends CI_Model {

    /**
     * Constructor
     *
     */
    public function __construct() {
        parent::__construct();
		$this->load->database();
    }


    function createMessage($from_id, $to_id, $job_id, $message) {
    	$data = array(
    		'from_user_id' => $from_id,
    		'to_user_id' => $to_id,
    		'job_id' => $job_id,
    		'message' => $message,
    		'sent_on' => date('Y-m-d H:i:s')
		);
        $this->db->insert('messages', $data);
        return $this->db->insert_id();
    }
	
	 function insertCensoredMessage($messages_id, $from_user_id, $raw_message) {
    	$data = array(
    		'messages_id' => $messages_id,
    		'user_id' => $from_user_id,
    		'original_message' => $raw_message
		);
        $this->db->insert('messages_censored', $data);
        return $this->db->insert_id();
    }
	
	function censorMessage($message) {
		$censored_message = preg_replace(
			'/[(]?\d{3,4}[)]?[-. ]?\d{3,4}[-. ]?\d{4,6}\b/i',
			'(phone censored)',
			$message
		); // censor phone

		$censored_message = preg_replace(
			"/([\w\.]+)@([\w\.]+)\.(\w+)/i",
			'(email censored)',
			$censored_message
		); // censor email
		
		return $censored_message;
	}


    function updateMessage($updateKey = array(), $updateData = array()) {
        $this->db->update('messages', $updateData, $updateKey);
    }


	function getMessages($my_id, $other_id, $job_id) {
	 	$this->db->select('*');
		$this->db->from('messages');
		$where = "( (from_user_id  = '".$my_id."' AND to_user_id  = '".$other_id."') OR
        		  (from_user_id = '".$other_id."' AND to_user_id = '".$my_id."') ) AND
        		  (job_id = '".$job_id."')";
		$this->db->where($where);
		$this->db->order_by('sent_on', 'ASC');
		$query = $this->db->get();
                
		return $query->result_array();
	 }

}

// End Message_model Class
?>
