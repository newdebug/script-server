<?php
/**
 * Created by PhpStorm.
 * User: Yuri
 * Date: 2018/3/29
 * Time: 18:37
 */

defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';

class Scripts extends REST_Controller
{
    function __construct ($config = 'rest')
    {
        parent::__construct($config);
        $this->load->model('script_model');
    }

    public function index_get ()
    {
        $id = $this->get('id');

        // If the id parameter doesn't exist return all the users with limit and offset
        if ($id === NULL)
        {
            $type = $this->get('type');
            $limit = $this->get('limit');
            $offset = $this->get('offset');
            $items = $this->script_model->get_scripts($type, $limit, $offset);
            if (!empty($items))
            {
                // Set the response and exit
                $this->response($items, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No scripts were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular user.
        $id = (int) $id;
        // Validate the id.
        if ($id <= 0)
        {
            // Invalid id, set the response and exit.
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid argument'
            ], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
        }

        $item = $this->script_model->get_script($id);
        if (!empty($item))
        {
            $this->set_response($item, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'User could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function index_post ()
    {

    }

    public function index_put ()
    {

    }

    public function index_delete ()
    {

    }
}