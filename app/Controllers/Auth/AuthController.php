<?php

namespace App\Controllers\Auth;

use App\Controller;
use App\Models\User;
use Rakit\Validation\Validator;

class AuthController extends Controller
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }

    public function showForm()
    {
        return view('auth.form');
    }

    public function login()
    {
        try {
            $data = $_POST;

            $validator = new Validator;

            $errors = $this->validate(
                $validator,
                $data,
                [
                    'email' => ['required', 'email'],
                    'password' => 'required|min:6|max:30',
                ]
            );
            $user = $this->user->getUserByEmail($data['email']);

            $checkPass = password_verify($data['password'], $user['password'] ?? null);

            if (!empty($errors)  || empty($user) || !$checkPass) {
                $_SESSION['status'] = false;
                $_SESSION['msg'] = 'Email hoặc mật khẩu không chính xác';
                $_SESSION['data'] = $_POST;
                $_SESSION['errors'] = $errors;
 

                if (empty($user) && !isset($_SESSION['errors']['email'])) {
                    $_SESSION['errors']['email'] = 'Email không tồn tại ';
                }
                if (isset($user['password']) && !$checkPass && !isset($_SESSION['errors']['password'])) {
                    $_SESSION['errors']['verify'] = 'Mật khẩu không đúng ';
                }
                redirect('/auth');
            } else {
                $_SESSION['data'] = null;
            }

            $_SESSION['user'] = $user;

            $redirectTo = ($_SESSION['user']['type'] == 'admin') ? '/admin' : '/';
            redirect($redirectTo);
        } catch (\Throwable $th) {
            $this->logError($th->__tostring());

            $_SESSION['status'] = false;
            $_SESSION['msg'] = 'Thao tác không thành công';
            $_SESSION['data'] = $_POST;

            redirect('/auth');
        }
    }

    public function register()
    {
        try {
            $data = $_POST;

            $validator = new Validator;

            $errors = $this->validate(
                $validator,
                $data,
                [
                    'name' => 'required|max:50',
                    'email' => [
                        'required', 
                        'email',
                        function ($value){
                            $flag = (new User)->checkExistsEmailForCreate($value);
                            if($flag){
                                return ":attribute has exists";
                            }
                        }
                ],
                'password' => 'required|min:6|max:30',
                'confirm_password' => 'required|same:password',
                ]
            );

            if (!empty($errors)) {
                $_SESSION['status'] = false;
                $_SESSION['msg'] = 'Thao tác không thành công ';
                $_SESSION['data'] = $_POST;
                $_SESSION['errors'] = $errors;

                redirect('/auth');
            } else {
                $_SESSION['data'] = null;
            }
            unset($data['confirm_password']);
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $data['type'] = 'client';

            $this->user->insert($data);

            $_SESSION['status'] = true;
            $_SESSION['msg'] = 'Thao tác thành công ';

            redirect('/auth');

        }catch (\Throwable $th) {
            $this->logError($th->__tostring());

            $_SESSION['status'] = false;
            $_SESSION['msg'] = 'Thao tác không thành công ';
            $_SESSION['data'] = $_POST;

            redirect('/auth');
        };

    }


    public function logout()
    {
        unset($_SESSION['user']);

        redirect('/');
    }
}
