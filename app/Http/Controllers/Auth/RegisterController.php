<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request; // Añadido por el metodo introducido
use Illuminate\Auth\Events\Registered;             // Añadido por el metodo introducido
use App\Role;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    //modificado para validarse con USERNAME envez de email
    protected $redirectTo = '/home';
    //protected $redirectTo = '/register';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        //modificado para validarse con USERNAME envez de email
        return Validator::make($data, [
            'username' => 'required|string|max:30|unique:users',
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */

    // metodo para añadir un Rol por defecto
    protected function create(array $data)
    {
        $user = User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
        ]);

        $user
            ->roles()
            ->attach(Role::where('name', 'user')->first());

        return $user;
    }
    /*
    protected function create(array $data)
    {
        //modificado para validarse con USERNAME envez de email
        return User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

    }
    */
// Añadimos este metodo para sobreescribir el metodo de vendor y no auto-loguee tras registrarse
    public function register(Request $request)
        {
            $this->validator($request->all())->validate();

            event(new Registered($user = $this->create($request->all())));

            // Para que no autologuee
            //$this->guard()->login($user); 


            return $this->registered($request, $user)
                            ?: redirect($this->redirectPath());
        }

}
