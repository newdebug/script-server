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
            $relation_table = $this->_tables['relation_script'];
            $this->_sql_select_join($this->db);
            $this->db->where("{$relation_table}.script_id", $id);
            $this->db->group_by("{$relation_table}.script_id");
            $query = $this->db->get();

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
        $relation_table = $this->_tables['relation_script'];
        $category_table = $this->_tables['category'];

        $this->_sql_select_join($this->db);
        $this->db->limit($limit);
        $this->db->offset($offset);
        $type <= 0 ?: $this->db->where("{$category_table}.category_id", $type);
        $this->db->group_by("{$relation_table}.script_id");
        $this->db->order_by("{$relation_table}.script_id", 'DESC'); // get latest scripts
        $query = $this->db->get();

        return $query->result_array();
    }

    public function create_script($data = array())
    {
        // insert data to script
        $script_table = $this->_tables['script'];
        $filtered_data = $this->_filter_data($script_table, $data);
        $ok = $this->db->insert($script_table, $filtered_data);
        $id = $ok ? $this->db->insert_id($script_table . 'script_id_seq') : null;

        // insert ids to relation table
        if( $id )
        {
            $category = $data['category'];// id
            $platforms = $data['platforms'];
            // platforms: array(
            // ['id' => 1, 'name' => '3dsmax', 'version'=>'2012', ...],
            // ['id' => 1, 'name' => '3dsmax', 'version'=>'2012', ...],
            // ['id' => 1, 'name' => '3dsmax', 'version'=>'2012', ...],
            // ...
            // );
            // or array(1,2,3,4,5, ...);
            foreach ($platforms as $p)
            {
                $rt = array(
                    'category_id'   => $category,
                    'script_id'     => $id,
                    'platform_id'   => $p,
                );
                $this->db->insert($this->_tables['relation_script'], $rt);
            }
        }

        return $id;
    }

    public function get_platforms($id = NULL)
    {
        $platform_table = $this->_tables['platform'];
        if( isset($id) )
        {
            $this->db->where('platform_id', $id);
            $query = $this->db->get($platform_table);

            return $query->row_array();
        }

        $query = $this->db->get($platform_table);
        return $query->result_array();
    }

    public function get_categories($id = NULL)
    {
        $category_table = $this->_tables['category'];
        if( isset($id) )
        {
            $this->db->where('category_id', $id);
            $query = $this->db->get($category_table);

            return $query->row_array();
        }

        $query = $this->db->get($category_table);
        return $query->result_array();
    }

/* *******************************************************************
 * PRIVATE FUNCTIONS
 * *******************************************************************
 */

    /**
     * @brief The _sql_select_join provides ...
     * @author Yuri Young<yuri.young@qq.com>
     * @param $db $this->db
     */
    private function _sql_select_join(&$db)
    {
        $script_table = $this->_tables['script'];
        $category_table = $this->_tables['category'];
        $platform_table = $this->_tables['platform'];
        $relation_table = $this->_tables['relation_script'];

        $sql = "{$script_table}.*, {$category_table}.*,GROUP_CONCAT(
                    {$platform_table}.version,'-',
                    {$platform_table}.type, ' ',
                    {$platform_table}.language SEPARATOR ', ') AS platform_version";

        $db->select($sql);
        $db->from($relation_table);
        $db->join($script_table, "{$script_table}.script_id={$relation_table}.script_id");
        $db->join($category_table, "{$category_table}.category_id={$relation_table}.category_id");
        $db->join($platform_table, "{$platform_table}.platform_id={$relation_table}.platform_id");
    }

    private function _filter_data($table = '', $data = array())
    {
        $filtered_data = array();
        $columns = $this->db->list_fields($table);

        if (is_array($data))
        {
            foreach ($columns as $column)
            {
                if (array_key_exists($column, $data))
                    $filtered_data[$column] = is_array($data[$column]) ? json_encode($data[$column]) : $data[$column];
            }
        }

        return $filtered_data;
    }
}