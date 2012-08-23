# Captcha

* Version : 1.1.0
* Author : Nachhatar Singh (Azoxy)
* Location : https://github.com/azoxy/CodeIgniter-Captcha

## About

Captcha spark library is based on CodeIgniter's core 'captcha' helper with additional features like:

* Better configuration management, many options available.
* Use of english words instead random text.
* Easy to validate captcha response.
* Immediate remove validated words to prevent reuse.
* Modified code for cleanup of expired captcha images/records.
* Minimal code for controllers and views (see example).

## Minimum Requirements

* CodeIgniter v2.1.0

## Installation

1. Install spark by copy files into sparks directory.
2. Copy your additional font files to 'system/fonts' directory.
3. Create 'images/captcha' directory to store captcha images.
4. Make sure 'images/captcha' directory is writeable.
5. Import code from 'sql/captcha.sql' into database.
6. Check & modify configuration 'config/captcha.php'.
7. Follow the example code, next.

Enjoy.

## Example

### Controller : Create Captcha Image

    if ($this->captcha->create()) {
        $data['captcha'] = $this->captcha->html_data;
    } else {
        $data['captcha'] = 'Captcha : ' . $this->captcha->debug;
    }
    $this->load->view('login', $data);

### Controller : Validate Response

    if ($this->captcha->check($this->input->post('captcha'))) {
        $message = "Success";
    } else {
        $message = "Failed";
    }

### View : Output Captcha Image

    <?php echo (!is_array($captcha)) ? $captcha : img($captcha); ?>
