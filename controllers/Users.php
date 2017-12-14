<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Users extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->library('session');
        $this->load->model('UsersModel');
        $this->load->library('encrypt');
    }
    
    
    
    
    public function check_userAuth()
    {
        $email = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);
        $pass = sha1($password);
        $data = $this->UsersModel->AuthUser($email, $pass);
        if(!empty($data) && $pass == $data->password && $data->status == '1'){
            echo "1";
        }else{
          echo "0";
        }
        
    }
    
    public function auth()
    {
        $email = $this->input->post('email', TRUE);
        $password = $this->input->post('password', TRUE);
        $pass = sha1($password);
        $data = $this->UsersModel->AuthUser($email, $pass);
        //$temp_check = $this->UsersModel->getTempCheck($password);
	
        $user_id = $data->user_id;
        $nice_name = $data->nice_name;
        $role = $data->role_id;
		$role_name = $data->role;
		$check_pass = $data->password;

        if ($pass == $check_pass) {
            $this->session->set_userdata('nice_name', $nice_name);
       	    $this->session->set_userdata('user_id', $user_id);
		    $this->session->set_userdata('role', $role);
		    $this->session->set_userdata('role_name', $role_name);

            if($role == '1') {
                redirect('administrator/');
            } else if($role == '2') {
                $id = $this->session->userdata('jobId');
                $update = $this->UsersModel->update_UserID($id, $user_id);
				$this->session->unset_userdata('jobId');
                redirect('homeowners/homepage');
            } else {
                redirect('tradesmen/homepage');
            }
        } else {
           // $this->session->set_userdata('msg', 'Either Password or Email Incorrect');
            $this->session->unset_userdata('user_id');
            $this->session->unset_userdata('nice_name');
            $this->session->set_userdata('role', $role);
            $this->session->unset_userdata('jobId');
            $this->session->sess_destroy();
            $data['msg'] = 'set';
            $this->load->view('includes/header');
            $this->load->view('index');
            $this->load->view('includes/loginmodal', $data);
            $this->load->view('includes/signupselectmodal');
            $this->load->view('tradesmen/tradessignupmodal');
            $this->load->view('includes/footer');
        }
        
 	}
    
    public function check_email()
    {
        $email = $this->input->post('forgot_email', TRUE);
        $data = $this->UsersModel->AuthEmail($email);
        $a_email = $data->email;
        
        if($email == $a_email){
            echo '1';
        }else{
            echo '0';
        }
    }
    
    public function forgot_password()
    {
        $email = $this->input->post('forgot_email', TRUE);
        $pass = $this->UsersModel->getRandom_password($email);
        $temp_pass = $pass->temp_password;
        $this->load->helper('mail_settings');
        $em_lib = new email_settings();
        $con = $em_lib->settings();
        $message = '
        Use these details to reset your password.<br>
        -------------------------------------------------
        <br>
        Email   : ' . $email . ' <br>
        Password: ' . $temp_pass . ' <br>
        -------------------------------------------------';
     
             
              $this->load->library('email', $con);
              $this->email->initialize($con);
              $this->email->set_newline("\r\n");
              $this->email->from('piece2gether.co.uk'); // change it to yours
              $this->email->to($email);// change it to yours
              $this->email->subject('Reset Password Request');
              $this->email->message(
                   $message   
                      );
              $this->email->send();
              $this->email->print_debugger();
              redirect('users/forgot_thankyou');
    }
    
    public function forgot_thankyou()
    {
        $this->load->view('includes/header');
        $this->load->view('forgot_thankyou');
        $this->load->view('includes/footer');
    }
    
    public function reset_password()
    {
        $this->load->view('includes/header');
        $this->load->view('reset_password');
        $this->load->view('includes/footer');
    }
    
    public function udpate_password()
    {
        $user_id = $this->session->userdata('user_id');
        $pass = $this->input->post('new_pass', TRUE);
        $new_pass = sha1($pass);
        $update = $this->UsersModel->updateNewPassword($user_id, $new_pass);
        if($update){
            redirect('users/message');
        }
    }
    
    public function message()
    {
        $this->load->view('includes/header');
        $this->load->view('message');
        $this->load->view('includes/footer');
    }
}