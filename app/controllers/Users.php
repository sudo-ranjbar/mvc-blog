<?php

class Users extends Controller
{
    private $userModel;

    public function __construct()
    {
        $this->userModel = $this->model('User');
    }

    public function register()
    {
        // Check for post
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process Form
            // Sanitize Post data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Init Data
            $data = [
                'name' => trim($_POST['name']),
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'confirm_password' => trim($_POST['confirm_password']),
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            // Validate Name
            if (empty($data['name'])) {
                $data['name_err'] = 'لطفا نام خود را وارد کنید';
            }

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'لطفا ایمیل خود را وارد کنید';
            } else {
                // Check email
                if ($this->userModel->findUserByEmail($data['email'])) {
                    $data['email_err'] = 'ایمیل قبلا انتخاب شده';
                }
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'لطفا پسورد خود را وارد کنید';
            } elseif (strlen($data['password']) < 6) {
                $data['password_err'] = 'پسورد باید بیشتر از 6 کاراکتر باشد';
            }

            // Validate Confirm Password
            if (empty($data['confirm_password'])) {
                $data['confirm_password_err'] = 'لطفا تکرار پسورد خود را وارد کنید';
            } elseif ($data['password'] != $data['confirm_password']) {
                $data['confirm_password_err'] = 'تکرار پسورد با پسورد مطابقت ندارد';
            }

            // Make Sure errors empty
            if (
                empty($data['name_err']) &&
                empty($data['email_err']) &&
                empty($data['password_err']) &&
                empty($data['confirm_password_err'])
            ) {
                // Validated

                // Hash Password
                $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);

                // Register User
                if ($this->userModel->register($data)) {
                    flash('register_success', 'شما عضو سایت شده اید و میتوانید وارد شوید');
                    redirect('users/login');
                } else {
                    die('Error User Registration');
                }
            } else {
                // Load VIew Register with errors
                $this->view('users/register', $data);
            }
        } else {
            // Init Data
            $data = [
                'name' => '',
                'email' => '',
                'password' => '',
                'confirm_password' => '',
                'name_err' => '',
                'email_err' => '',
                'password_err' => '',
                'confirm_password_err' => ''
            ];

            $this->view('users/register', $data);
        }
    }

    public function login()
    {
        // Check for post
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Process Form
            // Sanitize Post data
            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

            // Init Data
            $data = [
                'email' => trim($_POST['email']),
                'password' => trim($_POST['password']),
                'email_err' => '',
                'password_err' => ''
            ];

            // Validate Email
            if (empty($data['email'])) {
                $data['email_err'] = 'لطفا ایمیل خود را وارد کنید';
            } elseif ($this->userModel->findUserByEmail($data['email'])) {
                // Check for user/email
                // User Found
            } else {
                // User Not Found
                $data['email_err'] = 'چنین کاربری پیدا نشد';
            }

            // Validate Password
            if (empty($data['password'])) {
                $data['password_err'] = 'لطفا پسورد خود را وارد کنید';
            }

            // Make Sure errors empty
            if (
                empty($data['email_err']) &&
                empty($data['password_err'])
            ) {
                // Validated
                $loggedInUser = $this->userModel->login($data);
                if ($loggedInUser) {
                    // Create session
                    $this->createUserSession($loggedInUser);
                } else {
                    $data['password_err'] = 'پسورد را اشتباه وارد کرده اید';
                    $this->view('users/login', $data);
                }
            } else {
                // Load VIew Register with errors
                $this->view('users/login', $data);
            }
        } else {
            // Init Data
            $data = [
                'email' => '',
                'password' => '',
                'email_err' => '',
                'password_err' => ''
            ];

            $this->view('users/login', $data);
        }
    }

    public function createUserSession($user)
    {
        $_SESSION['user_id'] = $user->id;
        $_SESSION['user_email'] = $user->email;
        $_SESSION['user_name'] = $user->name;

        redirect('articles');
    }

    public function logout()
    {
        unset($_SESSION['user_id']);
        unset($_SESSION['user_email']);
        unset($_SESSION['user_name']);
        session_destroy();
        redirect('users/login');
    }
}
