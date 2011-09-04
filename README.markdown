# Captcha

Captcha spark library is based on CodeIgniter's core 'captcha' helper with additional features like:

* Better configuration management.
* Use of Google's simple captcha words database.
* Easy to validate/check captcha.
* Immediate remove validated words to prevent reuse.
* Modified code for cleanup of expired captcha images/records.
* Minimal code for controllers and views (see example).

## About

* Author : Nachhatar Singh (Azoxy)
* Location : https://github.com/azoxy/CodeIgniter-Captcha

## Requirements

* CodeIgniter 2.0.x

## Dependencies

* Libraries = database, session

## Installation

1. Install spark by copy files inro sparks directory.
2. Copy your additional font files to 'system/fonts' directory.
3. Make 'images/captcha' directory to store captcha images.
4. Import code from 'sql/INSTALL.mysql.txt' into database.
5. Follow the example code, next.
6. Enjoy.

## Example

### Controller : Create Captcha Image

    if($this->captcha->create()){
        $data['captcha'] = $this->captcha->html_data;
    } else {
        $data['captcha'] = 'Captcha : ' . $this->captcha->debug;
    }
    $this->load->view('login', $data);

### Controller : Check/Validate

    if ($this->captcha->check($this->input->post('captcha'))) {
    	$message = "Success";
    } else {
    	$message = "Failed";
    }

### View : Output Captcha Image

    <?php echo (!is_array($captcha)) ? $captcha : img($captcha); ?>
