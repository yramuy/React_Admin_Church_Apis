<?php

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/RestController.php';

use chriskacerguis\RestServer\RestController;

final class ApiDemoController extends RestController
{

	function index_get() {
		echo 'Hello';
	}
	
}

?>
