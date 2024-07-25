<?php
defined('BASEPATH') or exit('No direct script access allowed');

class User extends CI_Model
{
	function get_user_by_username($username)
	{

		$this->db->where('email', "$username")->or_where('user_name', "$username")->or_where('mobile_number ', "$username");
		$query = $this->db->get('tbl_user');
		$result =  $query->row();

		return $result;
	}

	function saveModule($data)
	{
		$query = $this->db->insert('tbl_modules', $data);

		return $query;
	}

	function getModules()
	{
		$baseUrl = base_url();
		$this->db->select('*');
		$this->db->from('tbl_modules');
		$this->db->order_by('id', 'DESC');
		$this->db->where('is_active', 0);
		$query = $this->db->get();
		$result = $query->result_array();
		$row_count = $query->num_rows();

		if ($row_count > 0) {
			for ($i = 0; $i < $row_count; $i++) {
				$output = array(
					'id' => $result[$i]['id'],
					'parent_id' => $result[$i]['parent_id'],
					'parent_name' => $this->getParentName($result[$i]['parent_id']),
					'name' => $result[$i]['name'],
					'role_id' => $result[$i]['role_id'],
					'role_name' => $this->getRoleName($result[$i]['role_id']),
					'iconName' => $result[$i]['icon'],
					'img_path' => $baseUrl . "assets/images/" . $result[$i]['icon']
				);

				$output1[] = $output;
			}
		} else {
			$output1 = [];
		}

		return $output1;
	}

	function getParentName($id)
	{
		$this->db->where('id', $id);
		$query = $this->db->get('tbl_modules');
		$result =  $query->row();
		$row_count = $query->num_rows();

		$name = $row_count > 0 ? $result->name : "";

		return $name;
	}

	function getRoleName($id)
	{
		if ($id == 1) {
			$role_name = 'Admin';
		} else if ($id == 2) {
			$role_name = 'Finance';
		} else if ($id == 3) {
			$role_name = 'Administrative';
		} else {
			$role_name = 'Saint';
		}

		return $role_name;
	}

	function getMenus($parentId, $roleId)
	{
		$query = $this->getMenuDataQuery($parentId, $roleId);
		$result = $query->result_array();
		$rowCount = $query->num_rows();
		$baseUrl = base_url();
		$output1 = array();
		for ($i = 0; $i < $rowCount; $i++) {
			$output = array(
				'id' => $result[$i]['id'],
				'parent_id' => $result[$i]['parent_id'],
				'parent_name' => $this->getParentName($result[$i]['parent_id']),
				'name' => $result[$i]['name'],
				'role_id' => $result[$i]['role_id'],
				'role_name' => $this->getRoleName($result[$i]['role_id']),
				'iconName' => $result[$i]['icon'],
				'img_path' => $baseUrl . "assets/images/" . $result[$i]['icon']
			);

			$output1[] = $output;
		}
		return $output1;
	}

	function getMenuDataQuery($parentId, $roleId)
	{
		$isActive = 0;
		$role = 4;
		$this->db->select('*');
		$this->db->from('tbl_modules');
		$this->db->where('parent_id', $parentId);
		if ($roleId != 1) {
			$this->db->where('role_id', $roleId);
		}		
		$this->db->where('is_active', $isActive);
		$this->db->or_where('role_id', $role);
		$query = $this->db->get();

		return $query;
	}
}
