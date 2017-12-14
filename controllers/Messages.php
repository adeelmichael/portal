<?php
require_once APPPATH.'../pusher/lib/Pusher.php';

class Messages extends CI_Controller {

    public $outputData;
    public $loggedInUser;

    /**
     * Constructor 
     *
     * Loads language files and models needed for this controller
     */
    public function __construct() {
        parent::__construct();
        $this->load->database();
		$this->load->helper('form');
        $this->load->library('session');
		$this->load->model('MessageModel');
		$this->load->model('TradesmenModel');
		$this->load->model('JobsModel');
		$this->load->model('UsersModel');
        //$this->config_data->db_config_fetch();
    }
	
	function test() {
		$message = 'This is a +441618659748 test from ihafkenschiel@gmail.com now';
		echo 'message: '.$message.'<br>';
		$censor = $this->MessageModel->censorMessage($message);
		echo 'censor: '.$censor.'<br>';
	}
	
	function send() {
		$from_id = $this->input->post('from_id', TRUE);
		$to_id = $this->input->post('to_id', TRUE);
		$job_id = $this->input->post('job_id', TRUE);
		$raw_message = $this->input->post('message', TRUE);
		
		// Censor for contact info
		$message = $this->MessageModel->censorMessage($raw_message);
		
		$messages_id = $this->MessageModel->createMessage($from_id, $to_id, $job_id, $message);
		
		if ( strcmp($raw_message, $message) != 0 ) { // message was censored
			$this->MessageModel->insertCensoredMessage($messages_id, $from_id, $raw_message); //store original message for admin to review
		}
		
		// Set up pusher for recipient to seamlessly display this new message
		$pusher_config = $this->config->item('pusher');
        $pusher = new Pusher($pusher_config['key'], $pusher_config['secret'], $pusher_config['app_id']);

        $pusher->trigger('privateChannel-User'.$to_id.'Job'.$job_id, 'sms:new', array(
            'message' => $message,
            'title' => 'New Message: '.$from_id
        ) );
	}
	
	public function loadHomeownerMessages() {
		$job_id = $this->uri->segment(3);
		$user_id = $this->session->userdata('user_id');
		$data['quotes'] = $this->JobsModel->get_jobQuotes($job_id);
		
    	// Load up messages for Chat modal
		$tradesmen_id = $this->uri->segment(4);
		$messages = $this->MessageModel->getMessages($user_id, $tradesmen_id, $job_id);
		
		
		$html = '<ul class="chat-wrap">';
	 	foreach ($messages as $message) {
	 		$my_user = $message['from_user_id'] == $user_id;
      	
			$html .= '<li class="'.($my_user ? 'sender' : 'user').'">
		            <div class="image">
		                <div class="item">
		                  <img src="'.base_url().'assets/images/chat/'.
		                  (  (!$my_user)  ? 'tradesmen' : 'blank_user' ).'.png" alt="">
		                </div>
		            </div>
		            <div class="text-wrap">
		                <div class="text-content">
		                   '.$message['message'].'
		                </div>
		            </div>
		        </li>';
	        
        } // end messages foreach loop
        $html .= "</ul>";
        
        echo $html;
	}
	
	public function loadTradesmenMessages() {
		$job_id = $this->uri->segment(3);
		$user_id = $this->session->userdata('user_id');
		$data = $this->TradesmenModel->single_jobDetails($job_id);
		
    	// Load up messages for Chat modal
		$homeowner_id = $data[0]['user_id'];
		$messages = $this->MessageModel->getMessages($user_id, $homeowner_id, $job_id);
		
		
		
		$html = '<ul class="chat-wrap">';
	 	foreach ($messages as $message) {
	 		$my_user = $message['from_user_id'] == $user_id;
      	
			$html .= '<li class="'.($my_user ? 'sender' : 'user').'">
		            <div class="image">
		                <div class="item">
		                  <img src="'.base_url().'assets/images/chat/'.
		                  ( ($my_user) ? 'tradesmen' : 'blank_user' ).'.png" alt="">
		                </div>
		            </div>
		            <div class="text-wrap">
		                <div class="text-content">
		                   '.$message['message'].'
		                </div>
		            </div>
		        </li>';
	        
        } // end messages foreach loop
        $html .= "</ul>";
        
        echo $html;
	}

	public function check_login()
    {
        $user_id = $this->session->userdata('user_id');
        if(!isset($user_id))
        {
            redirect('/');
        }
    }

    function tradesmen() {
    	$this->check_login();
    	$job_id = $this->uri->segment(3);
        $data['job_id'] = $job_id;
		$user_id = $this->session->userdata('user_id');
		$job_details = $this->TradesmenModel->single_jobDetails($job_id);
		$message_data['job_details'] = $job_details[0];
		
    	// Load up messages for Chat modal
		$homeowner_id = $message_data['job_details']['user_id'];
		$message_data['messages'] = $this->MessageModel->getMessages($user_id, $homeowner_id, $job_id);
		$message_data['my_user_id'] = $user_id;
		$message_data['other_user_id'] = $homeowner_id;
		
		$message_data['other_user']['l_name'] = $message_data['job_details']['l_name'];
		$message_data['other_user']['f_name'] = $message_data['job_details']['f_name'];
		
		//echo '<pre>';
		//var_dump($message_data);die();


        $this->load->view('tradesmen/header');
		$this->load->view('chat', $message_data);
        $this->load->view('tradesmen/footer');

 
	}

	function homeowners() {
    	$this->check_login();
        $job_id = $this->uri->segment(3);
		$user_id = $this->session->userdata('user_id');
		
        $data['details'] = $this->JobsModel->single_jobDetails($job_id);
		$message_data['job_details'] = $data['details'][0];
        $data['quotes'] = $this->JobsModel->get_jobQuotes($job_id);
		
		// Load up messages for Chat modal
		$tradesmen_id = $this->uri->segment(4);
		$message_data['messages'] = $this->MessageModel->getMessages($user_id, $tradesmen_id, $job_id);
		$message_data['my_user_id'] = $user_id;
		$message_data['other_user_id'] = $tradesmen_id;
		
		$message_data['other_user'] = $this->UsersModel->getUser($tradesmen_id);

		//echo '<pre>';
		//var_dump($message_data);
        $this->load->view('homeowners/header');
		$this->load->view('chat', $message_data);
        $this->load->view('homeowners/footer');
	}

}
