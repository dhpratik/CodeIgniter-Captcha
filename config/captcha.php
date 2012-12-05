<?php

/**
 * Name:  Captcha
 *
 * Version: 1.1.0
 *
 * Author: Nachhatar Singh (Azoxy)
 * Location: https://github.com/azoxy/CodeIgniter-Captcha
 *
 */

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 * Fonts directory path
 */
$config['font_path'] = BASEPATH . 'fonts/';

/**
 * Directory path, where captcha images will store
 */
$config['img_path'] = FCPATH . 'images/captcha/';

/**
 * Image (IMG) tag source (SRC) prefix url
 */
$config['img_url'] = '/images/captcha/';

/**
 * Captcha image width. Must be 180px minimum
 */
$config['img_width'] = 180;

/**
 * Captcha image height. Must be 45px minimum
 */
$config['img_height'] = 45;

/**
 * gaussian blur filter configuracion
 * Warning: slower image processing
 */
$config['apply_gaussian_blur_filter'] = FALSE;

/**
 * Captcha image expiration (seconds)
 */
$config['expiration'] = 900;

/**
 * Captcha text colors (R, G, B)
 */
$config['text_colors'] = array(
    array(27, 78, 181), // Blue
    array(22, 163, 35), // Green
    array(214, 36, 7) // Red
);

/**
 * Captcha image, background color (R, G, B)
 */
$config['background_color'] = array(255, 255, 255); // White

/**
 * Captcha image, background color (R, G, B)
 */
$config['border_color'] = array(204, 204, 204); // Light Gray

/**
 * Use Spiral pattern/lines in the background for distortion
 * Lines color (R, G, B)
 */
$config['distortion_lines'] = FALSE;
$config['distortion_lines_color'] = array(
    array(238, 238, 238), // Light Gray
    array(224, 224, 224) // Light Gray
);

/**
 * Captcha word uppercase or not (TRUE/FALSE)
 */
$config['captcha_word_uppercase'] = TRUE;

/**
 * Generate random Captcha word (TRUE/FALSE)
 */
$config['generate_random_word'] = FALSE;

/**
 * Generate random Captcha word. Do not use space or any special character
 */
$config['random_word_allowed_characters'] = '0123456789';

/**
 * Database tables
 * data_table = Store session/expiration information about captcha images.
 * words_table = Store words for captcha images.
 */
$config['data_table'] = 'captcha_sessions';
$config['words_table'] = 'captcha_words';
