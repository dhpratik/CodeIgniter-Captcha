<?php

if(!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Fonts directory path 
 */
$config['font_path'] = BASEPATH . 'fonts/';

/**
 * Directory path, where captcha images will store 
 */
$config['img_url'] = 'images/captcha/';

/**
 * Captcha image width. Must be 180px minimum
 */
$config['img_width'] = 180;

/**
 * Captcha image height. Must be 45px minimum
 */
$config['img_height'] = 45;

/**
 * Captcha image expiration
 */
$config['expiration'] = 900;

/**
 * Database tables
 * data_table = Store session/expiration information about captcha images.
 * words_table = Store words for captcha images.
 */
$config['data_table'] = 'captcha_sessions';
$config['words_table'] = 'captcha_words';
