<?php

defined('BASEPATH') or exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

class ApiUserController extends RestController
{

	public function __construct()

	{
		parent::__construct();
		// Load API Model
		// $this->load->database();
		// $this->load->vars($data);
		// Extract JSON Data
		$json = file_get_contents("php://input");
		// Convert The String Of Data To An Array
		$this->data = json_decode($json, true);
		$this->load->library('upload');
	}

	public function login_post()
	{

		$username = $this->data['username'];
		$password = $this->data['password'];
		$baseUrl = base_url();

		try {
			$user = $this->User->get_user_by_username($username);

			if ($user && password_verify($password, $user->user_password)) {
				// Successful Login
				// if ($user->user_role_id == 1) {
				// 	$role_name = 'Admin';
				// } else if ($user->user_role_id == 2) {
				// 	$role_name = 'Finance';
				// } else if ($user->user_role_id == 3) {
				// 	$role_name = 'Administrative';
				// } else {
				// 	$role_name = 'Saint';
				// }

				$response = array(
					'status' => '200',
					'message' => 'Logged In Successfully',
					'data' => [
						'id' => $user->id,
						'name' => $user->name,
						'username' => $user->user_name,
						'email' => $user->email,
						'role_id' => $user->user_role_id,
						'mobile_number' => $user->mobile_number,
						'role_name' => $this->User->getRoleName($user->user_role_id),
						'image_path' => $baseUrl . "assets/images/" . $user->image_name
					]
				);
				$this->output->set_status_header(200);
			} else {
				// Invalid credentials
				$response = array(
					'status' => '404',
					'message' => 'Invalid username or password'
				);
				$this->output->set_status_header(404);
			}
		} catch (Exception $e) {
			// Invalid credentials

			$response = array(
				'status' => '500',
				'message' => "Caught exception: " . $e->getMessage()
			);
			$this->output->set_status_header(500);
		}



		json_output($response['status'], $response);
	}

	public function saveModule_post()
	{
		$parentId = $this->input->post('parentId');
		$moduleName = $this->input->post('moduleName');
		$roleId = $this->input->post('roleId');

		$config = array(
			'file_name' => time(),
			// 'max_width' => '1028',
			// 'max_height' => '800',
			// 'max_size' => '24000000',
			'allowed_types' => 'gif|jpg|png|jpeg',
			'upload_path' => 'assets/images',
		);

		$this->upload->initialize($config);
		if ($this->upload->do_upload('icon')) {
			$imageData = $this->upload->data();
			// echo "pre"; print_r($this->upload->data());
		} else {
			echo $this->upload->display_errors();
			$response = array(
				'status' => '404',
				'message' => $this->upload->display_errors()
			);
			$this->output->set_status_header(404);
			die();
		}


		$data = array(
			'parent_id' => $parentId,
			'name' => $moduleName,
			'icon' => $imageData['file_name'],
			'role_id' => $roleId
		);

		$result = $this->User->saveModule($data);

		if ($result) {
			$response = array(
				'status' => '200',
				'message' => 'Module saved successfully'
			);
			$this->output->set_status_header(200);
		} else {
			$response = array(
				'status' => '404',
				'message' => 'Module save failed'
			);
			$this->output->set_status_header(404);
		}

		json_output($response['status'], $response);
	}

	function moduleList_get()
	{

		try {
			$modules = $this->User->getModules();
			$response = array(
				'status' => '200',
				'message' => sizeof($modules) > 0 ? 'Get module data successfully!' : "No module data found!",
				'modules' => $modules
			);
			$this->output->set_status_header(200);
		} catch (Exception $e) {
			$response = array(
				'status' => '404',
				'message' => 'Internal error',
				'modules' => "Error: " . $e->getMessage()
			);
			$this->output->set_status_header(404);
		}

		json_output($response['status'], $response);
	}

	function menus_post()
	{

		try {
			$parentId = $this->data['parent_id'];
			$roleId = $this->data['role_id'];

			$menus = $this->User->getMenus($parentId, $roleId);
			$response = array(
				'status' => '200',
				'message' => sizeof($menus) > 0 ? 'Get menus data successfully!' : "No menus data found!",
				'menus' => $menus
			);
			$this->output->set_status_header(200);
		} catch (Exception $e) {
			$response = array(
				'status' => '404',
				'message' => 'Internal error',
				'menus' => "Error: " . $e->getMessage()
			);
			$this->output->set_status_header(404);
		}

		json_output($response['status'], $response);
	}
}
