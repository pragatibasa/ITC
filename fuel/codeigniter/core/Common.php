<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use Aws\Ses\SesClient;
use Aws\Ses\Exception\SesException;

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP 5.1.6 or newer
 *
 * @package		CodeIgniter
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2008 - 2011, EllisLab, Inc.
 * @license		http://codeigniter.com/user_guide/license.html
 * @link		http://codeigniter.com
 * @since		Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

/**
 * Common Functions
 *
 * Loads the base classes and executes the request.
 *
 * @package		CodeIgniter
 * @subpackage	codeigniter
 * @category	Common Functions
 * @author		ExpressionEngine Dev Team
 * @link		http://codeigniter.com/user_guide/
 */

// ------------------------------------------------------------------------

/**
* Determines if the current version of PHP is greater then the supplied value
*
* Since there are a few places where we conditionally test for PHP > 5
* we'll set a static variable.
*
* @access	public
* @param	string
* @return	bool	TRUE if the current version is $version or higher
*/
	function is_php($version = '5.0.0')
	{
		static $_is_php;
		$version = (string)$version;

		if ( ! isset($_is_php[$version]))
		{
			$_is_php[$version] = (version_compare(PHP_VERSION, $version) < 0) ? FALSE : TRUE;
		}

		return $_is_php[$version];
	}

// ------------------------------------------------------------------------

/**
 * Tests for file writability
 *
 * is_writable() returns TRUE on Windows servers when you really can't write to
 * the file, based on the read-only attribute.  is_writable() is also unreliable
 * on Unix servers if safe_mode is on.
 *
 * @access	private
 * @return	void
 */
	function is_really_writable($file)
	{
		// If we're on a Unix server with safe_mode off we call is_writable
		if (DIRECTORY_SEPARATOR == '/' AND @ini_get("safe_mode") == FALSE)
		{
			return is_writable($file);
		}

		// For windows servers and safe_mode "on" installations we'll actually
		// write a file then read it.  Bah...
		if (is_dir($file))
		{
			$file = rtrim($file, '/').'/'.md5(mt_rand(1,100).mt_rand(1,100));

			if (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
			{
				return FALSE;
			}

			fclose($fp);
			@chmod($file, DIR_WRITE_MODE);
			@unlink($file);
			return TRUE;
		}
		elseif (($fp = @fopen($file, FOPEN_WRITE_CREATE)) === FALSE)
		{
			return FALSE;
		}

		fclose($fp);
		return TRUE;
	}

// ------------------------------------------------------------------------

/**
* Class registry
*
* This function acts as a singleton.  If the requested class does not
* exist it is instantiated and set to a static variable.  If it has
* previously been instantiated the variable is returned.
*
* @access	public
* @param	string	the class name being requested
* @param	string	the directory where the class should be found
* @param	string	the class name prefix
* @return	object
*/
	function &load_class($class, $directory = 'libraries', $prefix = 'CI_')
	{
		static $_classes = array();

		// Does the class exist?  If so, we're done...
		if (isset($_classes[$class]))
		{
			return $_classes[$class];
		}

		$name = FALSE;

		// Look for the class first in the native system/libraries folder
		// thenin the local application/libraries folder
		foreach (array(BASEPATH, APPPATH) as $path)
		{
			if (file_exists($path.$directory.'/'.$class.EXT))
			{
				$name = $prefix.$class;

				if (class_exists($name) === FALSE)
				{
					require($path.$directory.'/'.$class.EXT);
				}

				break;
			}
		}

		// Is the request a class extension?  If so we load it too
		if (file_exists(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.EXT))
		{
			$name = config_item('subclass_prefix').$class;

			if (class_exists($name) === FALSE)
			{
				require(APPPATH.$directory.'/'.config_item('subclass_prefix').$class.EXT);
			}
		}

		// Did we find the class?
		if ($name === FALSE)
		{
			// Note: We use exit() rather then show_error() in order to avoid a
			// self-referencing loop with the Excptions class
			exit('Unable to locate the specified class: '.$class.EXT);
		}

		// Keep track of what we just loaded
		is_loaded($class);

		$_classes[$class] = new $name();
		return $_classes[$class];
	}

// --------------------------------------------------------------------

/**
* Keeps track of which libraries have been loaded.  This function is
* called by the load_class() function above
*
* @access	public
* @return	array
*/
	function is_loaded($class = '')
	{
		static $_is_loaded = array();

		if ($class != '')
		{
			$_is_loaded[strtolower($class)] = $class;
		}

		return $_is_loaded;
	}

// ------------------------------------------------------------------------

/**
* Loads the main config.php file
*
* This function lets us grab the config file even if the Config class
* hasn't been instantiated yet
*
* @access	private
* @return	array
*/
	function &get_config($replace = array())
	{
		static $_config;

		if (isset($_config))
		{
			return $_config[0];
		}

		$file_path = APPPATH.'config/'.ENVIRONMENT.'/config'.EXT;

		// Fetch the config file
		if ( ! file_exists($file_path))
		{
			$file_path = APPPATH.'config/config'.EXT;
			
			if ( ! file_exists($file_path))
			{
				exit('The configuration file does not exist.');
			}
		}
	
		require($file_path);

		// Does the $config array exist in the file?
		if ( ! isset($config) OR ! is_array($config))
		{
			exit('Your config file does not appear to be formatted correctly.');
		}

		// Are any values being dynamically replaced?
		if (count($replace) > 0)
		{
			foreach ($replace as $key => $val)
			{
				if (isset($config[$key]))
				{
					$config[$key] = $val;
				}
			}
		}

$_config[0]=& $config;
return $_config[0];	
}

// ------------------------------------------------------------------------

/**
* Returns the specified config item
*
* @access	public
* @return	mixed
*/
	function config_item($item)
	{
		static $_config_item = array();

		if ( ! isset($_config_item[$item]))
		{
			$config =& get_config();

			if ( ! isset($config[$item]))
			{
				return FALSE;
			}
			$_config_item[$item] = $config[$item];
		}

		return $_config_item[$item];
	}

// ------------------------------------------------------------------------

/**
* Error Handler
*
* This function lets us invoke the exception class and
* display errors using the standard error template located
* in application/errors/errors.php
* This function will send the error page directly to the
* browser and exit.
*
* @access	public
* @return	void
*/
	function show_error($message, $status_code = 500, $heading = 'An Error Was Encountered')
	{
		$_error =& load_class('Exceptions', 'core');
		echo $_error->show_error($heading, $message, 'error_general', $status_code);
		exit;
	}

// ------------------------------------------------------------------------

/**
* 404 Page Handler
*
* This function is similar to the show_error() function above
* However, instead of the standard error template it displays
* 404 errors.
*
* @access	public
* @return	void
*/
	function show_404($page = '', $log_error = TRUE)
	{
		$_error =& load_class('Exceptions', 'core');
		$_error->show_404($page, $log_error);
		exit;
	}

// ------------------------------------------------------------------------

/**
* Error Logging Interface
*
* We use this as a simple mechanism to access the logging
* class and send messages to be logged.
*
* @access	public
* @return	void
*/
	function log_message($level = 'error', $message, $php_error = FALSE)
	{
		static $_log;

		if (config_item('log_threshold') == 0)
		{
			return;
		}

		$_log =& load_class('Log');
		$_log->write_log($level, $message, $php_error);
	}

// ------------------------------------------------------------------------

/**
 * Set HTTP Status Header
 *
 * @access	public
 * @param	int		the status code
 * @param	string
 * @return	void
 */
	function set_status_header($code = 200, $text = '')
	{
		$stati = array(
							200	=> 'OK',
							201	=> 'Created',
							202	=> 'Accepted',
							203	=> 'Non-Authoritative Information',
							204	=> 'No Content',
							205	=> 'Reset Content',
							206	=> 'Partial Content',

							300	=> 'Multiple Choices',
							301	=> 'Moved Permanently',
							302	=> 'Found',
							304	=> 'Not Modified',
							305	=> 'Use Proxy',
							307	=> 'Temporary Redirect',

							400	=> 'Bad Request',
							401	=> 'Unauthorized',
							403	=> 'Forbidden',
							404	=> 'Not Found',
							405	=> 'Method Not Allowed',
							406	=> 'Not Acceptable',
							407	=> 'Proxy Authentication Required',
							408	=> 'Request Timeout',
							409	=> 'Conflict',
							410	=> 'Gone',
							411	=> 'Length Required',
							412	=> 'Precondition Failed',
							413	=> 'Request Entity Too Large',
							414	=> 'Request-URI Too Long',
							415	=> 'Unsupported Media Type',
							416	=> 'Requested Range Not Satisfiable',
							417	=> 'Expectation Failed',

							500	=> 'Internal Server Error',
							501	=> 'Not Implemented',
							502	=> 'Bad Gateway',
							503	=> 'Service Unavailable',
							504	=> 'Gateway Timeout',
							505	=> 'HTTP Version Not Supported'
						);

		if ($code == '' OR ! is_numeric($code))
		{
			show_error('Status codes must be numeric', 500);
		}

		if (isset($stati[$code]) AND $text == '')
		{
			$text = $stati[$code];
		}

		if ($text == '')
		{
			show_error('No status text available.  Please check your status code number or supply your own message text.', 500);
		}

		$server_protocol = (isset($_SERVER['SERVER_PROTOCOL'])) ? $_SERVER['SERVER_PROTOCOL'] : FALSE;

		if (substr(php_sapi_name(), 0, 3) == 'cgi')
		{
			header("Status: {$code} {$text}", TRUE);
		}
		elseif ($server_protocol == 'HTTP/1.1' OR $server_protocol == 'HTTP/1.0')
		{
			header($server_protocol." {$code} {$text}", TRUE, $code);
		}
		else
		{
			header("HTTP/1.1 {$code} {$text}", TRUE, $code);
		}
	}

// --------------------------------------------------------------------

/**
* Exception Handler
*
* This is the custom exception handler that is declaired at the top
* of Codeigniter.php.  The main reason we use this is to permit
* PHP errors to be logged in our own log files since the user may
* not have access to server logs. Since this function
* effectively intercepts PHP errors, however, we also need
* to display errors based on the current error_reporting level.
* We do that with the use of a PHP error template.
*
* @access	private
* @return	void
*/
	function _exception_handler($severity, $message, $filepath, $line)
	{
		 // We don't bother with "strict" notices since they tend to fill up
		 // the log file with excess information that isn't normally very helpful.
		 // For example, if you are running PHP 5 and you use version 4 style
		 // class functions (without prefixes like "public", "private", etc.)
		 // you'll get notices telling you that these have been deprecated.
		if ($severity == E_STRICT)
		{
			return;
		}

		$_error =& load_class('Exceptions', 'core');

		// Should we display the error? We'll get the current error_reporting
		// level and add its bits with the severity bits to find out.
		if (($severity & error_reporting()) == $severity)
		{
			$_error->show_php_error($severity, $message, $filepath, $line);
		}

		// Should we log the error?  No?  We're done...
		if (config_item('log_threshold') == 0)
		{
			return;
		}

		$_error->log_exception($severity, $message, $filepath, $line);
	}

	// --------------------------------------------------------------------

	/**
	 * Remove Invisible Characters
	 *
	 * This prevents sandwiching null characters
	 * between ascii characters, like Java\0script.
	 *
	 * @access	public
	 * @param	string
	 * @return	string
	 */
	function remove_invisible_characters($str)
	{
		static $non_displayables;

		if ( ! isset($non_displayables))
		{
			// every control character except newline (dec 10), carriage return (dec 13), and horizontal tab (dec 09),
			$non_displayables = array(
										'/%0[0-8bcef]/',			// url encoded 00-08, 11, 12, 14, 15
										'/%1[0-9a-f]/',				// url encoded 16-31
										'/[\x00-\x08]/',			// 00-08
										'/\x0b/', '/\x0c/',			// 11, 12
										'/[\x0e-\x1f]/'				// 14-31
									);
		}

		do
		{
			$cleaned = $str;
			$str = preg_replace($non_displayables, '', $str);
		}
		while ($cleaned != $str);

		return $str;
	}

	function getServiceAccountingCode($billType) {
		$otherManufacturing = array( 'Cutting', 'Slitting', 'Recoiling' );
		$warehousingStorage = array( 'SemiFinished','Directbilling' );
		if(in_array($billType, $otherManufacturing)) {
			return '(Other Manufacturing Service) : 998898'; 
		} elseif (in_array($billType, $warehousingStorage)) {
			return '(Warehousing and Storage Services) : 996729'; 
		}
	}

//	function sendSMS($contact,$msg) {
//// Account details
//        $apiKey = urlencode('riQ0XJ3yyrA-ccu7j4FzGWSNGV1EsQeFqe07LPUOy7');
//
//        // Message details
//        $numbers = array($contact);
//        $sender = urlencode('ASPENS');
//        $message = rawurlencode($msg);
//
//        $numbers = implode(',', $numbers);
//
//        // Prepare data for POST request
//        $data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
//
//        // Send the POST request with cURL
//        $ch = curl_init('https://api.textlocal.in/send/');
//        curl_setopt($ch, CURLOPT_POST, true);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//        $response = curl_exec($ch);
//        curl_close($ch);
//
//        // Process your response here
//        echo $response;
//    }

function companyHeader($str = '') {
	$CI =& get_instance();
	$companyData = $CI->fuel_auth->company_data();

	$strHeader = '<table width="100%"  cellspacing="0" cellpadding="5" border="0">
		'.$str.'
		
		<tr>
		<td align="left" width="50" height="50">
		   <img src="ISP LOGO.jpg"  alt="logo">
		</td>
			<td align="center" width="80%" style="font-size:20px;">Subject to Chennai Jurisdiction<br>TAX INVOICE <br> Supply of Service</td>			
		</tr>
		
		<tr>
			<td width="100%"align="center" style="font-size:30px; font-style:bold; font-family: Times New Roman;"><h1>'.$companyData->company_name.' </h1></td>			
		</tr>
		

		<tr>
			<td align="center" width="100%" style="font-size:25px; font-style:bold;
			font-family: Times New Roman;"><b>'.$companyData->head_address.' <br/> State Name: Tamil Nadu,Code:33</b> <br/> (Decoiling / Service Centre Steel Industry)
			<br/> 
			Phone No:'.$companyData->contact.'&nbsp;
			Email:'.$companyData->email.' 
			 <br/> GST Regn. No: '.$companyData->gst_no.'
			&nbsp; PAN No:'.$companyData->pan.'</td>
		</tr>
		
	</table>';
	

	return $strHeader;
}


    function billingFooter() {
        $CI =& get_instance();
        $companyData = $CI->fuel_auth->company_data();

	    $strFooter = '<tr>
				<td width="60%">
					<b>Received the above goods in good condition.</b>
				</td>
				<td width="30%"><b>For '.$companyData->company_name.'.</b></td>
			</tr>';

	    return $strFooter;
    }

//    function sendEmail($recipients, $subject, $body, $filePath = '') {
//        $sender = 'info@aspensteel2.com';
//        $senderName = 'Aspen Steel Pvt Ltd';
//        $recipient = $recipients;
//        $usernameSmtp = 'AKIARC37NFLSIAWUVBOA';
//
//        $passwordSmtp = 'BGJH/OrzJvAwb0NXipmX5Nt9vCd3kViMVW+IlAEk30wK';
//
//        $configurationSet = 'aspensteel2';
//        $host = 'email-smtp.us-east-1.amazonaws.com';
//        $port = 587;
//
//        $mail = new PHPMailer(true);
//
//        try {
//            // Specify the SMTP settings.
//            $mail->isSMTP();
//            $mail->setFrom($sender, $senderName);
//            $mail->Username   = $usernameSmtp;
//            $mail->Password   = $passwordSmtp;
//            $mail->Host       = $host;
//            $mail->Port       = $port;
//            $mail->SMTPAuth   = true;
//            $mail->SMTPSecure = 'tls';
//            $mail->addCustomHeader('X-SES-CONFIGURATION-SET', $configurationSet);
//
//            // Specify the message recipients.
//            $mail->addAddress($recipient);
//            // You can also add CC, BCC, and additional To recipients here.
//
//            // Specify the content of the message.
//            $mail->isHTML(true);
//            $mail->Subject    = $subject;
//            $mail->Body       = $body;
//            if( $filePath != '' )
//                $mail->addAttachment($filePath);
//
//            $mail->Send();
//            echo "Email sent!" , PHP_EOL;
//            if( $filePath != '' )
//                unlink($filePath);
//        } catch (phpmailerException $e) {
//            echo "An error occurred. {$e->errorMessage()}", PHP_EOL; //Catch errors from PHPMailer.
//        } catch (Exception $e) {
//            echo "Email not sent. {$mail->ErrorInfo}", PHP_EOL; //Catch errors from Amazon SES.
//        }
//    }

	function show($variable) {
	    print_r('<pre>');
	    print_r($variable);
	    print_r('</pre>');
	    exit;
    }

/* End of file Common.php */
/* Location: ./system/core/Common.php */