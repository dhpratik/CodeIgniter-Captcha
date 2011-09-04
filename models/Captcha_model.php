<?php

if(!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Captcha Model
 */
class Captcha_model extends CI_Model
{
    public $tables;

    /**
     * Captcha_model::__construct()
     * 
     * @return
     */
    public function __construct()
    {
        $this->load->config('captcha', TRUE);
        $this->tables['words_table'] = $this->config->item('words_table', 'captcha');
        $this->tables['data_table'] = $this->config->item('data_table', 'captcha');
    }

    /**
     * Captcha_model::get_word()
     * 
     * @return string
     */
    public function get_word()
    {
        $word = '';
        $query = $this->db->query('SELECT word FROM ' . $this->tables['words_table'] . ' ORDER BY RAND() LIMIT 1');
        if($query->num_rows() == 1)
        {
            $row = $query->row();
            $word = $row->word;
        }
        return $word;
    }

    /**
     * Captcha_model::remove_expired()
     * 
     * @param integer $expiration
     */
    public function remove_expired($expiration = 0)
    {
        $this->db->query('DELETE FROM ' . $this->tables['data_table'] . ' WHERE captcha_time < ' . $expiration);
    }

    /**
     * Captcha_model::remove_single()
     * 
     * @param string $post_word
     */
    public function remove_single($post_word = '')
    {
        $binds = array($post_word, $this->input->ip_address());
        $sql = 'DELETE FROM ' . $this->tables['data_table'] . ' WHERE word = ? AND ip_address = ?';
        $query = $this->db->query($sql, $binds);
    }

    /**
     * Captcha_model::check()
     * 
     * @param string $post_word
     * @param integer $expiration
     * @return integer
     */
    public function check($post_word = '', $expiration = 0)
    {
        $sql = 'SELECT COUNT(*) AS count FROM ' . $this->tables['data_table'] . ' WHERE word = ? AND ip_address = ? AND captcha_time > ?';
        $binds = array($post_word, $this->input->ip_address(), $expiration);
        $query = $this->db->query($sql, $binds);
        $row = $query->row();
        return $row->count;
    }

    /**
     * Captcha_model::store()
     * 
     * @param mixed $data
     */
    public function store($data)
    {
        $query = $this->db->insert_string($this->tables['data_table'], $data);
        $this->db->query($query);
    }
}
