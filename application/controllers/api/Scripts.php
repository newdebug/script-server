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
            $type = (int)$this->get('type');
            $limit = (int)$this->get('limit');
            $offset = (int)$this->get('offset');
            if ($type < 0 OR $limit < 0 OR $offset < 0)
            {
                $this->response([
                    'status' => FALSE,
                    'message' => 'Invalid argument'
                ], REST_Controller::HTTP_BAD_REQUEST); // BAD_REQUEST (400) being the HTTP response code
            }

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
                    'message' => 'No resource were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

        // Find and return a single record for a particular user.
        $id = (int)$id;
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
                'message' => 'Resource could not be found'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
    }

    public function index_post ()
    {
        $data = $this->post();
        $id = $this->script_model->create_script($data);

        $message = [
            'id' => $id,
            'resource' => $data,
            'message' => 'Added a resource successful'
        ];

        $this->set_response($message, REST_Controller::HTTP_CREATED); // CREATED (201) being the HTTP response code

    }

    public function index_put ()
    {

    }

    public function index_delete ()
    {

    }

    public function platform_get ()
    {
        $id = $this->get('id');
        $items = $this->script_model->get_platforms($id);
        if (!empty($items))
        {
            $this->set_response($items, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Resource could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function category_get ()
    {
        $id = $this->get('id');
        $items = $this->script_model->get_categories($id);
        if (!empty($items))
        {
            $this->set_response($items, REST_Controller::HTTP_OK);
        }
        else
        {
            $this->set_response([
                'status' => FALSE,
                'message' => 'Resource could not be found'
            ], REST_Controller::HTTP_NOT_FOUND);
        }
    }

    public function upload_post ()
    {
        $this->load->library('upload');
        $save_dir = 'uploads/images';
        $config = array();
        $config['upload_path'] = FCPATH . $save_dir;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size'] = 1024 * 2; // 2MB
        $config['overwrite'] = false;
        $config['remove_spaces'] = true;//remove sapces from file
        $uploaded_files = array();

        foreach ($_FILES as $key => $value)
        {
            if (strlen($value['name']) > 0)
            {
                $this->upload->initialize($config);
                if (!$this->upload->do_upload($key))
                {
                    $uploaded_files[$key] = array(
                        "status" => false,
                        "message" => $value['name'] . ': ' . $this->upload->display_errors());
                }
                else
                {
                    $uploaded_files[$key] = array("status" => true, "info" => $this->upload->data());
                }
            }
        }

        return $uploaded_files;
    }
}