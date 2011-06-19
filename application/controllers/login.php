<?php defined('SYSPATH') or die('No direct script access.');
/**
 * This controller handles login requests.
 *
 * PHP version 5
 * LICENSE: This source file is subject to LGPL license 
 * that is available through the world-wide-web at the following URI:
 * http://www.gnu.org/copyleft/lesser.html
 * @author     Ushahidi Team <team@ushahidi.com> 
 * @package    Ushahidi - http://source.ushahididev.com
 * @module     Login Controller  
 * @copyright  Ushahidi - http://www.ushahidi.com
 * @license    http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License (LGPL) 
 */

class Login_Controller extends Template_Controller {
	
    public $auto_render = TRUE;
	
    protected $user;
	
    // Session Object
    protected $session;
	
    // Main template
    public $template = 'admin/login';
	

    public function __construct()
    {
        parent::__construct();
		
        $this->session = new Session();
		// $profiler = new Profiler;
    }
	
    public function index()
    {
        $auth = Auth::instance();
		
        // If already logged in redirect to user account page
        // Otherwise attempt to auto login if autologin cookie can be found
        // (Set when user previously logged in and ticked 'stay logged in')
        if ($auth->logged_in() OR $auth->auto_login())
        {
            if ($user = Session::instance()->get('auth_user',FALSE))
            {
                url::redirect('admin/dashboard');
            }
        }
				
		
        $form = array(
	        'username' => '',
	        'password' => '',
                );

        //  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
        $errors = $form;
        $form_error = FALSE;
		
		
        // Set up the validation object
        $_POST = Validation::factory($_POST)
            ->pre_filter('trim')
            ->add_rules('username', 'required')
            ->add_rules('password', 'required');
		
        if ($_POST->validate())
        {
            // Sanitize $_POST data removing all inputs without rules
            $postdata_array = $_POST->safe_array();

            // Load the user
            $user = ORM::factory('user', $postdata_array['username']);

            // If no user with that username found
            if (! $user->id)
            {
                $_POST->add_error('username', 'login error');
            }
            else
            {
                $remember = (isset($_POST['remember']))? TRUE : FALSE;

                // Attempt a login
                if ($auth->login($user, $postdata_array['password'], $remember))
                {
                    url::redirect('admin/dashboard');
                }
                else
                {
                    $_POST->add_error('password', 'login error');
                }
            }
            // repopulate the form fields
            $form = arr::overwrite($form, $_POST->as_array());
			
            // populate the error fields, if any
            // We need to already have created an error message file, for Kohana to use
            // Pass the error message file name to the errors() method			
            $errors = arr::overwrite($errors, $_POST->errors('auth'));
            $form_error = TRUE;
        }
		
        $this->template->errors = $errors;
        $this->template->form = $form;
        $this->template->form_error = $form_error;
    }
    
    /**
     * Reset password upon user request.
     */
    public function resetpassword()
    {
    	$auth = Auth::instance();
		
        // If already logged in redirect to user account page
        // Otherwise attempt to auto login if autologin cookie can be found
        // (Set when user previously logged in and ticked 'stay logged in')
        if ($auth->logged_in() OR $auth->auto_login())
        {
            if ($user = Session::instance()->get('auth_user',FALSE))
            {
                url::redirect('admin/dashboard');
            }
        }
    	
    	$this->template = new View('admin/reset_password');
		
		$this->template->title = Kohana::lang('ui_admin.password_reset');
		$form = array
	    (
			'email' 	=> '',
	    );
		
		//  copy the form as errors, so the errors will be stored with keys corresponding to the form field names
	    $errors = $form;
		$form_error = FALSE;
		$password_reset = FALSE;
		$form_action = "";

		// check, has the form been submitted, if so, setup validation
	    if ($_POST)
	    {
	        $post = Validation::factory($_POST);

	         //  Add some filters
	        $post->pre_filter('trim', TRUE);

	        // Add some rules, the input field, followed by a list of checks, carried out in order
			$post->add_rules('email','required','email','length[4,64]');

			$post->add_callbacks('email', array($this,'email_exists_chk'));

			if ($post->validate())
	    	{
				$user = ORM::factory('user',$post->email);
				
				// Existing User??
				if ($user->loaded==true)
				{
					// Secret consists of email and the last_login field.
					// So as soon as the user logs in again, 
					// the reset link expires automatically.
					$secret = $auth->hash_password($user->email.$user->last_login);
					$secret_link = url::site('login/newpassword/'.$user->id.'/'.$secret);
					
					$details_sent = $this->_email_resetlink($post->email,$user->name,$secret_link);
					if( $details_sent )
					{
						$password_reset = TRUE;
					}		
				}

			}
            else
            {
				// repopulate the form fields
	            $form = arr::overwrite($form, $post->as_array());

	            // populate the error fields, if any
	            $errors = arr::overwrite($errors, $post->errors('auth'));
				$form_error = TRUE;
			}
	    }

        $this->template->form = $form;
	    $this->template->errors = $errors;
		$this->template->form_error = $form_error;
		$this->template->password_reset = $password_reset;
		$this->template->form_action = $form_action;

		// Javascript Header
		//TODO create reset_password js file.
		$this->template->js = new View('admin/reset_password_js');
	}

    public function newuser($user_id = false)
    {
	$form = array
	(
	  'name' 	=> '',
	  'email'	=> '',
	  'username'    => '',
	  'notify'      => '',
	);
	$errors = $form;
	$form_error = FALSE;
	$form_action = "";
	
	if($_POST)
	{
            $post = Validation::factory($_POST);

            //  Add some filters
            $post->pre_filter('trim', TRUE);
    
            $post->add_rules('username','required','length[3,16]', 'alpha');
            
	    //only validate password as required when user_id has value.
            $post->add_rules('name','required','length[3,100]');
            $post->add_rules('email','required','email','length[4,64]');
            $user_id == '' ? $post->add_callbacks('username',
                array($this,'username_exists_chk')) : '';
            $user_id == '' ? $post->add_callbacks('email',
                array($this,'email_exists_chk')) : '';
            $post->add_rules('notify','between[0,1]');
            
            if ($post->validate())
	    { // Email New Password
                $user = ORM::factory('user',$user_id);
                $user->name = $post->name;
		$user->username = $post->username;
                $user->email = $post->email;
                $user->notify = $post->notify;
                   
                // Add New Roles
                $user->add(ORM::factory('role', 'login'));
                $user->add(ORM::factory('role', 'reporter'));
		   
		$new_password = $this->_generate_password();
		$user->password = $new_password;
		$user->save();
		
		$this->_email_newuser( $user->email, $user->name, $user->username, $new_password);
		//url::redirect(url::site().'login/created_user');

		$this->template = new View('admin/created_user');
		return;
	    }
            else 
            {
                // repopulate the form fields
                $form = arr::overwrite($form, $post->as_array());

                // populate the error fields, if any
                $errors = arr::overwrite($errors, $post->errors('auth'));
                $form_error = TRUE;
            }
        }

	$this->template = new View('admin/new_user');
	$this->template->form = $form;
	$this->template->errors = $errors;
	$this->template->form_error = $form_error;
	$this->template->form_action = $form_action;
	$this->template->user =  "";// user model's user
	$this->template->yesno_array = array('1'=>strtoupper(Kohana::lang('ui_main.yes')),'0'=>strtoupper(Kohana::lang('ui_main.no')));        
    }
    /**
     * Create New password upon user request.
     */
    public function newpassword($user_id = 0)
    {
    	$auth = Auth::instance();
		// If already logged in redirect to user account page
        // Otherwise attempt to auto login if autologin cookie can be found
        // (Set when user previously logged in and ticked 'stay logged in')
        if ($auth->logged_in() OR $auth->auto_login())
        {
            if ($user = Session::instance()->get('auth_user',FALSE))
            {
                url::redirect('admin/dashboard');
            }
        }
    	
    	$this->template = new View('admin/new_password');
		
		$this->template->title = Kohana::lang('ui_admin.new_password');
		
		$secret = $this->uri->segment(4);
		$user = ORM::factory('user',$user_id);
		if ($user->loaded == true && 
			$auth->hash_password($user->email.$user->last_login, $auth->find_salt($secret)) == $secret)
		{ // Email New Password
			$new_password = $this->_generate_password();
			$user->password = $new_password;
			$user->save();
			
			$this->_email_newpassword( $user->email, $user->name, $user->username, $new_password);
		}
		else
		{ // User doesn't exist or reset link expired - redirect to login
			url::redirect('admin/');
		}		
	}

	/**
	 * Checks if username already exists.
	 * @param Validation $post $_POST variable with validation rules 
	 */
	public function username_exists_chk(Validation $post)
	{
	    $users = ORM::factory('user');
	    // If add->rules validation found any errors, get me out of here!
	    if (array_key_exists('username', $post->errors()))
	        return;
			
	    if ($users->username_exists($post->username))
	        $post->add_error( 'username', 'exists');
	}

	/**
	 * Checks if email address is associated with an account.
	 * @param Validation $post $_POST variable with validation rules
	 */
	public function email_exists_chk( Validation $post )
	{
		$users = ORM::factory('user');
		if( array_key_exists('email',$post->errors()))
			return;

		if( !$users->email_exists( $post->email ) )
			$post->add_error('resetemail','invalid');
	}

	/**
	 * Generate random password for the user.
	 *
 	 * @return the new password
	 */
	public function _generate_password()
	{
		$password_chars = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";
		$chars_length = strlen( $password_chars ) - 1;
		$password = NULL;
		for( $i = 0; $i < 8; $i++ )
		{
			$position = mt_rand(0,$chars_length);
			$password .= $password_chars[$position];
		}
		return $password;
	}	
	
	/**
	 * Email reset link to the user.
	 * 
	 * @param the email address of the user requesting a password reset.
	 * @param the username of the user requesting a password reset.
	 * @param the new generated password.
	 * 
	 * @return void.
	 */
	public function _email_resetlink( $email, $name, $secret_url )
	{
		$to = $email;
		$from = Kohana::lang('ui_admin.password_reset_from');
		$subject = Kohana::lang('ui_admin.password_reset_subject');
		$message = Kohana::lang('ui_admin.password_reset_message_line_1').' '.$name.",\n";
		$message .= Kohana::lang('ui_admin.password_reset_message_line_2').' '.$name.". ";
		$message .= Kohana::lang('ui_admin.password_reset_message_line_3')."\n\n";
		$message .= $secret_url."\n\n";
		
		//email details
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	
	}
	
	public function _email_newuser( $email, $name, $username, $password )
	{
		$to = $email;
		$from = Kohana::lang('ui_admin.password_reset_from');
		$subject = Kohana::lang('ui_admin.user_create');
		
		$message = Kohana::lang('ui_admin.user_create_message_line_1').' '.$name.",\n";
		$message .= Kohana::lang('ui_admin.user_create_message_line_4').":\n\n";
		$message .= Kohana::lang('ui_admin.label_username').": ".$username."\n";
		$message .= Kohana::lang('ui_admin.password').": ".$password;
		
		//email details
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	
	}
	
	public function _email_newpassword( $email, $name, $username, $password )
	{
		$to = $email;
		$from = Kohana::lang('ui_admin.password_reset_from');
		$subject = Kohana::lang('ui_admin.password_reset_subject');
		
		$message = Kohana::lang('ui_admin.password_reset_message_line_1').' '.$name.",\n";
		$message .= Kohana::lang('ui_admin.password_reset_message_line_4').":\n\n";
		$message .= Kohana::lang('ui_admin.label_username').": ".$username."\n";
		$message .= Kohana::lang('ui_admin.password').": ".$password;
		
		//email details
		if( email::send( $to, $from, $subject, $message, FALSE ) == 1 )
		{
			return TRUE;
		}
		else 
		{
			return FALSE;
		}
	
	}	
}
