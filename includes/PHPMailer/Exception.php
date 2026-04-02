<?php
/**
 * PHPMailer Exception class.
 * PHP Version 5.5.
 *
 * @see https://github.com/PHPMailer/PHPMailer/
 * @author  Marcus Bointon (Synchro/coolbru) <phpmailer@synchromedia.co.uk>
 * @license LGPL 2.1
 */
namespace PHPMailer\PHPMailer;
class Exception extends \Exception
{
    public function errorMessage()
    {
        return '<strong>' . htmlspecialchars($this->getMessage()) . "</strong><br />\n";
    }
}
