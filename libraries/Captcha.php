<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Name:  Captcha
 *
 * Version: 1.1.0
 *
 * Author: Nachhatar Singh (Azoxy)
 * Location: https://github.com/azoxy/CodeIgniter-Captcha
 *
 */
class Captcha {

    public $html_data;
    public $debug;
    private $config;
    private $CI;
    private $expiration;
    private $current_time;

    /**
     * Captcha::construct()
     *
     * @param array $param
     */
    public function __construct($param = array()) {

        $this->CI = &get_instance();

        $this->CI->load->library('session');
        $this->CI->load->model('captcha_model');

        $this->CI->load->config('captcha', TRUE);
        $this->config = $this->CI->config->item('captcha');

        $this->config['word'] = $this->generate_word();

        if (is_array($param)) {
            foreach ($this->config as $key => $val) {
                if (!empty($param[$key])) {
                    $this->config[$key] = $param[$key];
                }
            }
        }

        $this->html_data = array(
            'src' => '',
            'width' => $this->config['img_width'],
            'height' => $this->config['img_height'],
            'border' => 0,
            'alt' => '',
        );

        $this->expiration = time() - $this->config['expiration'];

        list($usec, $sec) = explode(" ", microtime());
        $this->current_time = ((float) $usec + (float) $sec);

        $this->clean();
    }

    /**
     * Captcha::create()
     *
     * @return bool
     */
    public function create() {

        if (empty($this->config['img_path']) || empty($this->config['img_url'])) {
            $this->debug = 'img_path or img_url is empty';
            return FALSE;
        }

        if (!@is_dir($this->config['img_path'])) {
            $this->debug = 'img_path is not valid';
            return FALSE;
        }

        if (!is_writable($this->config['img_path'])) {
            $this->debug = 'img_path is not writable';
            return FALSE;
        }

        if (!extension_loaded('gd')) {
            $this->debug = 'GD extension not available';
            return FALSE;
        }

        /**
         * Check available fonts
         */
        $fonts = array();
        $handle = opendir($this->config['font_path']);
        if ($handle) {
            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != "index.html") {
                    if (substr(strrchr(strtolower($file), "."), 1) == 'ttf') {
                        $fonts[] = $file;
                    }
                }
            }
            @closedir($handle);
        }

        /**
         * Check font_path
         */
        $font_path = $this->config['font_path'] . $fonts[rand(0, (count($fonts) - 1))];
        $use_font = ($font_path != '' && file_exists($font_path) && function_exists('imagettftext')) ? TRUE : FALSE;

        if ($use_font == FALSE) {
            $this->debug = 'font_path is not valid';
            $this->config['img_width'] = 150;
            $this->config['img_height'] = 30;
        }

        /**
         * Do we have a "word" yet?
         */
        if ($this->config['word'] == '') {
            $this->config['word'] = $this->generate_word();
        }

        /**
         * Determine angle and position
         */
        $length = strlen($this->config['word']);
        $angle = ($length >= 6) ? rand(-($length - 6), ($length - 6)) : 0;
        $x_axis = rand(6, (360 / $length) - 16);
        $y_axis = ($angle >= 0) ? rand($this->config['img_height'], $this->config['img_width']) : rand(6, $this->config['img_height']);

        /**
         * Create image
         *
         * PHP.net recommends imagecreatetruecolor(), but it isn't always available
         */
        if (function_exists('imagecreatetruecolor')) {
            $im = imagecreatetruecolor($this->config['img_width'], $this->config['img_height']);
        } else {
            $im = imagecreate($this->config['img_width'], $this->config['img_height']);
        }

        /**
         * Assign colors
         */
        $rgb = $this->config['background_color'];
        $bg_color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);

        $rgb = $this->config['border_color'];
        $border_color = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);

        $grid_color = array();
        foreach ($this->config['distortion_lines_color'] as $rgb) {
            $grid_color[] = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        }

        $text_color = array();
        foreach ($this->config['text_colors'] as $rgb) {
            $text_color[] = imagecolorallocate($im, $rgb[0], $rgb[1], $rgb[2]);
        }

        /**
         * Create the rectangle
         */
        ImageFilledRectangle($im, 0, 0, $this->config['img_width'], $this->config['img_height'], $bg_color);

        /**
         * Create the spiral pattern/lines in the background for distortion
         */
        if ($this->config['distortion_lines']) {
            $theta = 1;
            $thetac = 7;
            $radius = 16;
            $circles = 20;
            $points = 32;
            for ($i = 0; $i < ($circles * $points) - 1; $i++) {
                $theta = $theta + $thetac;
                $rad = $radius * ($i / $points);
                $x = ($rad * cos($theta)) + $x_axis;
                $y = ($rad * sin($theta)) + $y_axis;
                $theta = $theta + $thetac;
                $rad1 = $radius * (($i + 1) / $points);
                $x1 = ($rad1 * cos($theta)) + $x_axis;
                $y1 = ($rad1 * sin($theta)) + $y_axis;
                imageline($im, $x, $y, $x1, $y1, $grid_color[rand(0, count($grid_color) - 1)]);
                $theta = $theta - $thetac;
            }
        }

        /**
         * Write the text
         */
        if ($use_font == FALSE) {
            $font_size = 6;
            $x = rand(0, $this->config['img_width'] / ($length / 3));
            $y = 0;
        } else {
            $font_size = 18;
            $x = rand(0, $this->config['img_width'] / ($length / 1.5));
            $y = $font_size + 2;
        }
        for ($i = 0; $i < strlen($this->config['word']); $i++) {
            if ($use_font == FALSE) {
                $y = rand(0, $this->config['img_height'] / 2);
                imagestring($im, $font_size, $x, $y, substr($this->config['word'], $i, 1), $text_color[rand(0, count($text_color) - 1)]);
                $x += ($font_size * 2);
            } else {
                $y = rand($this->config['img_height'] / 2, $this->config['img_height'] - 3);
                imagettftext($im, $font_size, $angle, $x, $y, $text_color[rand(0, count($text_color) - 1)], $font_path, substr($this->config['word'], $i, 1));
                $x += $font_size;
            }
        }

        /**
         * apply gaussian blur filter
         */
        if ($this->config['apply_gaussian_blur_filter'] && function_exists('imagefilter')) {
            imagefilter($im, IMG_FILTER_GAUSSIAN_BLUR);
        }

        /**
         * Create the border
         */
        imagerectangle($im, 0, 0, $this->config['img_width'] - 1, $this->config['img_height'] - 1, $border_color);

        /**
         * Generate the image
         */
        $img_name = $this->current_time . '.jpg';
        imagejpeg($im, $this->config['img_path'] . $img_name);
        imagedestroy($im);

        $this->html_data['src'] = $this->config['img_url'] . $img_name;

        $data = array(
            'captcha_time' => $this->current_time,
            'ip_address' => $this->CI->input->ip_address(),
            'word' => $this->config['word']
        );
        $this->CI->captcha_model->store($data);

        return TRUE;
    }

    /**
     * Captcha::check()
     *
     * @param string $post_word
     * @return bool
     */
    public function check($post_word = '') {

        if (empty($post_word)) {
            return FALSE;
        }

        if ($this->config['captcha_word_uppercase']) {
            $post_word = strtoupper($post_word);
        }

        if ($this->CI->captcha_model->check($post_word, $this->expiration) == 0) {
            return FALSE;
        } else {
            $this->CI->captcha_model->remove_single($post_word);
            return TRUE;
        }
    }

    /**
     * Captcha::clean()
     */
    private function clean() {

        $this->CI->captcha_model->remove_expired($this->expiration);

        /**
         * Remove old images physically
         */
        $current_dir = @opendir($this->config['img_path']);
        while ($filename = @readdir($current_dir)) {
            if ($filename != "." and $filename != ".." and $filename != "index.html") {
                $name = str_replace(".jpg", "", $filename);
                if (($name + $this->config['expiration']) < $this->current_time) {
                    @unlink($this->config['img_path'] . $filename);
                }
            }
        }

        @closedir($current_dir);
    }

    /**
     * Captcha::generate_word()
     *
     * @return string
     */
    private function generate_word() {

        if (!$this->config['generate_random_word']) {
            $word = $this->CI->captcha_model->get_word();
        }

        if (empty($word)) {
            $pool = $this->config['random_word_allowed_characters'];
            $pool_length = strlen($pool);
            for ($i = 0; $i < 8; $i++) {
                $word .= substr($pool, mt_rand(0, $pool_length - 1), 1);
            }
        }

        if ($this->config['captcha_word_uppercase']) {
            $word = strtoupper($word);
        }

        return $word;
    }

}
