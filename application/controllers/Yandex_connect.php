<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Yandex_connect extends CI_Controller 
{
    
    public function __construct()
    {
        parent::__construct();

        $this->load->library('table');
        $this->load->library('yandex_connect_library');

        $this->load->model('yandex_connect_model');
    }


    /**
     * List All Applications
     */
    public function list_applications()
    {
        $applications = $this->yandex_connect_model->get_all();

        $this->load->view('application/list', [
            'applications' => $applications
        ]);
    }


    /**
     * Show Add New Application Form
     */
    public function add_application()
    {
        if($this->input->post('submit_application')):
            $this->post_add_application();
        endif;

        $this->load->view('application/add');
    }


    /**
     * Show Edit Application Form
     */
    public function edit_application($id)
    {
        if($this->input->post('submit_application')):
            $this->post_edit_application();
        endif;

        $application = $this->yandex_connect_model->get_ID($id);

        $this->load->view('application/add', [
            'application' => $application
        ]);
    }    


    /**
     * Add New Application
     */
    public function post_add_application()
    {
        $this->form_validation->set_rules('app_name', 'App Name', 'trim|required');
        $this->form_validation->set_rules('app_id', 'App ID', 'trim|required');
	$this->form_validation->set_rules('app_secret', 'App Secret', 'trim|required');
        
        if($this->form_validation->run() == FALSE):
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => validation_errors('<p class="mb-0">', '</p>')
                ]
            ]);

            return false;
        endif;

        $app_name = $this->input->post('app_name');
        $app_id = $this->input->post('app_id');
        $app_secret = $this->input->post('app_secret');

        $add = $this->yandex_connect_model->add(compact('app_name', 'app_id', 'app_secret'));

        if($add):
            redirect( base_url('yandex_connect/list_applications') );
        else:
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => 'Failed to add application, something went wrong'
                ]
            ]);            
        endif;
    }


    /**
     * Edit Application
     */
    public function post_edit_application()
    {
        $this->form_validation->set_rules('id', 'ID', 'trim|required');
        $this->form_validation->set_rules('app_name', 'App Name', 'trim|required');
        $this->form_validation->set_rules('app_id', 'App ID', 'trim|required');
	$this->form_validation->set_rules('app_secret', 'App Secret', 'trim|required');
        
        if($this->form_validation->run() == FALSE):
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => validation_errors('<p class="mb-0">', '</p>')
                ]
            ]);

            return false;
        endif;

        $id = $this->input->post('id');
        $app_name = $this->input->post('app_name');
        $app_id = $this->input->post('app_id');
        $app_secret = $this->input->post('app_secret');

        $update = $this->yandex_connect_model->update($id, compact('app_name', 'app_id', 'app_secret'));

        if($update):
            redirect( base_url('yandex_connect/list_applications') );
        else:
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => 'Failed to update application, something went wrong'
                ]
            ]);            
        endif;
    }


    /**
     * Delete Application
     */
    public function delete_application($id) 
    {                
        $delete_application = $this->yandex_connect_model->delete_ID($id);

        if($delete_application):
            $this->session->set_flashdata([
                'success' => true,
                'data' => [
                    'msg' => 'Application deleted'
                ]
            ]);            
        else:
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => 'Failed to delete application, something went wrong'
                ]
            ]);            
        endif;

        redirect( base_url('yandex_connect/list_applications') );
    }


    /**
     * Authorize For Access Token
     */
    public function authorize($id)
    {
        $data = $this->yandex_connect_model->get_ID($id);

        $app_id = $data->app_id;

        $qs = [];
        $qs['response_type'] = 'code';
        $qs['client_id'] = $app_id;
        $qs['force_confirm'] = 'no';
        $qs['redirect_uri'] = base_url('yandex_connect/get_access_token');

        $this->session->set_flashdata('app_id', $id);

        redirect("https://oauth.yandex.com/authorize?".http_build_query($qs));
    }


    /**
     * Getting Access Token
     */
    public function get_access_token()
    {
        if($this->input->get('code')):

            $id = $this->session->flashdata('app_id');

            $data = $this->yandex_connect_model->get_ID($id);

            $app_id = $data->app_id;
            $app_secret = $data->app_secret;

            $code = $this->input->get('code');
            
            $query = [];
            $query['grant_type'] = 'authorization_code';
            $query['code'] = $code;
            $query['client_id'] = $app_id;
            $query['client_secret'] = $app_secret;

            $query = http_build_query($query);

            $result = $this->yandex_connect_library->get_access_token($query);
            $result = json_decode($result, false);

            if($result->access_token):
                $access_token = $result->access_token;
                $refresh_token = $result->refresh_token;
                $expires_in = $result->expires_in;

                $expire = time() + $expires_in;
                $expire = date('Y-m-d H:i:s', $expire);

                $this->db->set([
                    "access_token" => $access_token,
                    "refresh_token" => $refresh_token,
                    "expires_in" => $expires_in,
                    "expire_date" => $expire
                ])->update("yandex_connect");
                
                $this->session->set_flashdata([
                    'success' => true,
                    'data' => [
                        'msg' => 'Access Token Received'
                    ]
                ]);

                redirect( base_url('yandex_connect/list_applications') );
            endif;                      
        endif;
    }


    /**
     * Listing All Organizations
     */
    public function list_organizations($id)
    {
        $sql = $this->yandex_connect_model->get_ID($id);

        $access_token = $sql->access_token;

        /**
         * Settings Access Token For E-Mai Actions
         */
        $this->session->set_userdata([
            'access_token' => $access_token
        ]);

	$header = [];
	$header[] = 'Content-type: application/json';
        $header[] = 'Authorization: OAuth '.$access_token;
        
	$query = [];
	$query["fields"] = "domains";
        $query = http_build_query($query);

        $get_organizations = $this->yandex_connect_library->get_organizations($query, $header);
        $http_code = $get_organizations['http_code'];
        $result = $get_organizations['result'];
        
	$result = json_decode($result);
		
	if($http_code == 200):
            $success = true;
            $data = [
                'result' => $result->result
            ];
        else:
            $success = false;
            $data = [
                'http_code' => $http_code,
                'message' => $result->message
            ];
        endif;

        $this->load->view('organization/list', [
            'success' => $success, 
            'data' => $data
        ]);
    }

    /**
     * Listing All Accounts In Selected Organization
     */
	public function list_accounts($organization_id)
	{
        $header   	= [];
	$header[] 	= 'Content-length: 0';
	$header[] 	= 'Content-type: application/json';
	$header[] 	= 'Authorization: OAuth '.$this->session->userdata('access_token');
	$header[] 	= 'X-Org-ID: '.$organization_id;
		
	$query		= [];
	$query["fields"] = "name,email,gender,created,position,contacts,is_robot,is_dismissed,is_admin";
        
        $query = http_build_query($query);
        
        $get_accounts = $this->yandex_connect_library->get_accounts($query, $header);
        $http_code = $get_accounts['http_code'];
        $result = $get_accounts['result'];

	$result = json_decode($result, false);
		
	if($http_code == 200):
		$data = [
                	'success' => true,
                	'organization_id' => $organization_id,
                	'result' => $result
		];
	else:
		$data = [
			'success' => false,
			'data' => [
				'msg' => $result->message,
				'code' => $result->code
			]
		];
        endif;
                
	$this->load->view('account/list', $data);
    }
    

    /**
     * Show Add New Account Form
     */
    public function add_account($organization_id)
    {
        if($this->input->post('add_account')):
            $this->post_add_account();
        endif;

        $this->load->view('account/add', [
            'organization_id' => $organization_id
        ]);       
    }


    /**
     * Show Update Account Form
     */
    public function edit_account($organization_id, $id)
    {
        if($this->input->post('edit_account')):
            $this->post_edit_account();
        endif;

        $this->load->view('account/edit', [
            'organization_id' => $organization_id,
            'id' => $id
        ]);        
    }    


    /**
     * Adding New Account
     */
    public function post_add_account()
    {
        $this->form_validation->set_rules('nickname', 'Username', 'trim|required|min_length[3]');
        $this->form_validation->set_rules('name_first', 'First Name', 'trim|required|min_length[3]');
	$this->form_validation->set_rules('name_last', 'Last Name', 'trim|required|min_length[3]');
	$this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[20]');
        
        if($this->form_validation->run() == FALSE):
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => validation_errors('<p class="mb-0">', '</p>')
                ]
            ]);

            return false;
        endif;

        $organization_id = $this->input->post('organization_id');
        $nickname = $this->input->post('nickname');
        $name_first = $this->input->post('name_first');
        $name_last = $this->input->post('name_last');
        $password = $this->input->post('password');
        
        $header   	= [];
        $header[] 	= 'Content-type: application/json';
        $header[] 	= 'Authorization: OAuth '.$this->session->userdata('access_token');
        $header[] 	= 'X-Org-ID: '.$organization_id;
        
        $query = [];
        $query["department_id"] = 1;
        $query["nickname"] = $nickname;;
        $query["password"] = $password;
        $query["name"]["first"] = $name_first;
        $query["name"]["last"] = $name_last;
        $query = json_encode($query);

        $add_account = $this->yandex_connect_library->add_account($query, $header);
        
        $http_code = $add_account['http_code'];
        $result = $add_account['result'];

        $result = json_decode($result, false);
        
        if($http_code == 201):
            $this->session->set_flashdata([
                'success' => true,
                'data' => [
                    'msg' => 'Adding new mail address'
                ]
            ]);

            redirect( base_url('yandex_connect/list_accounts/'.$organization_id) );			
        else:
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => $result->message.", Http Code: ".$http_code
                ]
            ]);
        endif;
    }


    /**
     * Updating Existing Account
     */
    public function post_edit_account()
    {
        $this->form_validation->set_rules('password', 'Password', 'trim|required|min_length[6]|max_length[20]');

        if($this->form_validation->run() == FALSE):
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => validation_errors('<p class="mb-0">', '</p>')
                ]
            ]);

            return false;
        endif;

        $organization_id = $this->input->post('organization_id');
        $id = $this->input->post('id');
        $password = $this->input->post('password');

	$header   	= [];
	$header[] 	= 'Content-type: application/json';
	$header[] 	= 'Authorization: OAuth '.$this->session->userdata('access_token');
	$header[] 	= 'X-Org-ID: '.$organization_id;
			
	$query = [];
        $query["password"] = $password;
        $query = json_encode($query);
        
        $update_account = $this->yandex_connect_library->update_account($id, $query, $header);

        $http_code = $update_account['http_code'];
        $result = $update_account['result'];

        $result = json_decode($result, false);

        if($http_code == 200):
            $this->session->set_flashdata([
                'success' => true,
                'data' => [
                    'msg' => 'Password is updated'
                ]
            ]);

            redirect( base_url('yandex_connect/list_accounts/'.$organization_id) );	
        else:
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => $result->message.", Http Code: ".$http_code
                ]
            ]);
        endif;
    }

    /**
     * Deleting Account
     */
    public function delete_account($organization_id, $id) 
    {        
        $header   	= [];
        $header[] 	= 'Content-type: application/json';
        $header[] 	= 'Authorization: OAuth '.$this->session->userdata('access_token');
        $header[] 	= 'X-Org-ID: '.$organization_id;
        
        $query		= [];
        $query['is_dismissed'] = true;
        $query = json_encode($query);
        
        $delete_account = $this->yandex_connect_library->delete_account($id, $query, $header);

        $http_code = $delete_account['http_code'];
        $result = $delete_account['result'];

        $result = json_decode($result, false);

        if($http_code == 200):
            $this->session->set_flashdata([
                'success' => true,
                'data' => [
                    'msg' => 'E-mail address deleted'
                ]
            ]);            
        else:
            $this->session->set_flashdata([
                'success' => false,
                'data' => [
                    'msg' => $result->message
                ]
            ]);            
        endif;

        redirect( base_url('yandex_connect/list_accounts/'.$organization_id) );
    }
}
