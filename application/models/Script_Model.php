<?php
/**
 * Created by PhpStorm.
 * User: Yuri
 * Date: 2018/3/29
 * Time: 19:31
 */

defined('BASEPATH') OR exit('No direct script access allowed');

class Script_Model extends CI_Model
{
    private $_tables = [];

    function __construct()
    {
        $this->config->load('api', TRUE);
        $this->_tables = $this->config->item('tables', 'api');
    }

    /**
     * @brief The get_script provides query a script file with given id value
     * @author Yuri Young<yuri.young@qq.com>
     * @param null $id
     * @return array
     */
    public function get_script($id = NULL)
    {
        if (isset($id) && is_numeric($id))
        {
            $query = $this->db->get_where($this->_tables['script'], array('script_id' => $id));
            return $query->row_array();
        }

        return array();
    }

    /**
     * @brief The get_scripts provides query script files with given limit and offset value
     * @author Yuri Young<yuri.young@qq.com>
     * @param int $limit default limit 10
     * @param int $offset default offset 0
     * @return array
     */
    public function get_scripts($type = 0, $limit = 10, $offset = 0)
    {
        $this->db->order_by('id', 'DESC'); // get latest scripts

        $limit = $limit ?: 10;
        $offset = $offset ?: 0;
        $type === NULL ?:  $this->db->where('category_id', $type);
        $query = $this->db->get($this->_tables['script'], $limit, $offset);
        return $query->result_array();
    }

    public function create_script()
    {

    }
}