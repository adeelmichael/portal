<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Administrator extends CI_Controller {
    
    public function __construct() {
        parent::__construct();
        $this->load->helper('form');
        $this->load->helper('cookie');
        
        $this->load->library('session');
        $this->load->model('AdminModel');
        $this->load->library('encrypt');
        
        //ckeditor
        $this->load->library('ckeditor');
        $this->load->library('ckfinder');
    }
    
    
    public function index()
    {
        $this->check_login();
        $data['profile'] = $this->AdminModel->get_all_profiles();
        $data['media'] = $this->AdminModel->get_all_profiles();
        $this->load->view('admin/header');
        $this->load->view('admin/idVerifications', $data);
        $this->load->view('admin/footer');
    }
    
    //login check
     public function check_login()
    {
        $user_id = $this->session->userdata('user_id');
        if(!isset($user_id))
        {
            redirect('/');
        }
    }
    
    //logout
     public function logout()
    {
        $this->session->unset_userdata('user_id');
        $this->session->unset_userdata('nice_name');
        $this->session->sess_destroy();
        redirect('/');
    }
    
    
    public function deny()
    {
        $this->check_login();
        $user_id = $this->input->post('user_id', TRUE);
        $media_id = $this->input->post('denyMd_id', TRUE);
        $email = $this->input->post('v_email', TRUE);
        $details = $this->input->post('deny_details', TRUE);
        set_cookie('id',$user_id,'3600'); 
        set_cookie('email',$email,'3600'); 
        set_cookie('denyMd_id',$media_id,'3600'); 
       
        $s_email = get_cookie('email');
        $s_id = get_cookie('id');
        $md_id = get_cookie('denyMd_id');
        
        if(isset($_POST['submit'])){
        $this->load->helper('mail_settings');
        $em_lib = new email_settings();
        $con = $em_lib->settings();
       
        $message = '
        Your ID has been Denied due the reasons-. <br>
        Details   : ' . $details. '
        ';
     
             
              $this->load->library('email', $con);
              $this->email->initialize($con);
              $this->email->set_newline("\r\n");
              $this->email->from('piece2gether.co.uk'); // change it to yours
              $this->email->to($s_email);// change it to yours
              $this->email->subject('ID Verification');
              $this->email->message(
                   $message   
                      );
              $this->email->send();
              $this->email->print_debugger();
              
              $update = $this->AdminModel->update_denial($details, $s_id, $md_id);
              delete_cookie('id');
              delete_cookie('email');
              delete_cookie('denyMd_id');
              redirect('administrator/');
        }
    }
    
    public function verify()
    {
         $this->check_login();
         $user_id = $this->input->post('user_id', TRUE);
         $media_id = $this->input->post('media_id', TRUE);
         $email = $this->input->post('verif_email', TRUE);
        $this->load->helper('mail_settings');
        $em_lib = new email_settings();
        $con = $em_lib->settings();
        $message = '
        Your ID has been verified. You can login and apply on jobs';
     
             
              $this->load->library('email', $con);
              $this->email->initialize($con);
              $this->email->set_newline("\r\n");
              $this->email->from('piece2gether.co.uk'); // change it to yours
              $this->email->to($email);// change it to yours
              $this->email->subject('ID Verification');
              $this->email->message(
                   $message   
                      );
              $this->email->send();
              $this->email->print_debugger();
              $update = $this->AdminModel->update_verification($media_id);
              
              redirect('administrator/');
    }
    
    
    public function view_tradesmenProfile()
    {
        $this->check_login();
        $user_id = $this->uri->segment(3);
        $data['profiles'] = $this->AdminModel->gettradersData($user_id);
        $data['meta'] = $this->AdminModel->getsignleMeta($user_id);
        $this->load->view('admin/header');
        $this->load->view('admin/view_profiles', $data);
        $this->load->view('admin/footer');
    }
    
    public function post()
    {
        $this->check_login();
        $this->ckeditor->basePath = base_url().'assets/ckeditor/';
        $this->ckeditor->config['toolbar'] = array(
                        array( 'Source', '-', 'Bold', 'Italic', 'Underline', '-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','NumberedList','BulletedList' )
                                                            );
        $this->ckeditor->config['language'] = 'it';
        $this->ckeditor->config['width'] = '100%';
        $this->ckeditor->config['height'] = '300px';            
        
        $this->load->view('admin/header');
        $this->load->view('admin/post_page');
        $this->load->view('admin/footer');
    }
    
    public function publish_post()
    {
        $this->check_login();
        $user_id = $this->session->userdata('user_id');
        $title = $this->input->post('post_title', TRUE);
        $content = strip_tags($this->input->post('content', TRUE));
        if (isset($_FILES['file']['name'])) {
                $image = $_FILES['file']['name'];
                $config['upload_path'] = 'blog_media/';
                $config['file_name'] = $image;
                $config['overwrite'] = false;
                $config["allowed_types"] = '*';
                $config["max_size"] = '*';
                $this->load->library('upload', $config);

                $imagePath = "blog_media/".$_FILES['file']['name'];
                
                    $this->upload->do_upload('file');
                    echo $this->upload->display_errors();
                
                $add = $this->AdminModel->save_post($user_id, $title, $content, $imagePath);
                if($add){
                    $this->session->set_userdata('success', 'Post Published');
                    redirect('administrator/view_posts');
                }
            }
    }
    
    //view posts
    public function view_posts()
    {
        $this->check_login();
        $data['posts'] = $this->AdminModel->get_blog_posts();
        $this->load->view('admin/header');
        $this->load->view('admin/blog', $data);
        $this->load->view('admin/footer');
    }
    
    //edit posts
    public function edit_post()
    {
        $this->check_login();
        $this->ckeditor->basePath = base_url().'assets/ckeditor/';
        $this->ckeditor->config['toolbar'] = array(
                        array( 'Source', '-', 'Bold', 'Italic', 'Underline', '-','Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo','-','NumberedList','BulletedList' )
                                                            );
        $this->ckeditor->config['language'] = 'it';
        $this->ckeditor->config['width'] = '730px';
        $this->ckeditor->config['height'] = '300px';  
        $post_id = $this->uri->segment(3);
        $data['post'] = $this->AdminModel->get_post_byId($post_id);
        $this->load->view('admin/header');
        $this->load->view('admin/edit_post', $data);
        $this->load->view('admin/footer');
    }
    
    public function update_post()
    {
        $user_id = $this->session->userdata('user_id');
        $this->check_login();
        $post_id = $this->input->post('post_id', TRUE);
        $title = $this->input->post('post_title', TRUE);
        $content = strip_tags($this->input->post('content', TRUE));
        $postedfile = $this->input->post('hid_file', TRUE);
        if ($_FILES['file']['name']) {
                $image = $_FILES['file']['name'];
                $config['upload_path'] = 'blog_media/';
                $config['file_name'] = $image;
                $config['overwrite'] = false;
                $config["allowed_types"] = '*';
                $config["max_size"] = '*';
                $this->load->library('upload', $config);
                
                $imagePath = "blog_media/" . $_FILES['file']['name'];
                
                    $this->upload->do_upload('file');
                    echo $this->upload->display_errors();
               
            }
            if(isset($imagePath)){
                 $add = $this->AdminModel->update_postdata($user_id, $title, $content, $imagePath, $post_id);
                if($add){
                   redirect('administrator/view_posts');
                }
            }else{
               $imagePath = $postedfile;
               $add = $this->AdminModel->update_postdata($user_id, $title, $content, $imagePath, $post_id);
                if($add){
                   redirect('administrator/view_posts');
                }
            }
    }
    
    public function delete_post()
    {
        $this->check_login();
        $post_id = $this->uri->segment(3);
        $del = $this->AdminModel->delete_post_data($post_id);
        if($del){
            redirect('administrator/view_posts');
        }
    }
    
    
    public function delete_blogImage()
    {
        $post_id = $this->uri->segment(3);
        $this->check_login();
        $img = $this->input->post('im', TRUE);
        $success = $this->AdminModel->del_img($img);
        if($success){
         echo "1";
        }else{
            echo "0";
        }
    }
}