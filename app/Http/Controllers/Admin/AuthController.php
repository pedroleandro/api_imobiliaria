<?php

namespace LaraDev\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use LaraDev\Contract;
use LaraDev\Http\Controllers\Controller;
use LaraDev\Property;
use LaraDev\User;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        if (Auth::check() === true) {
            return redirect()->route('admin.home');
        }

        return view('admin.index');
    }

    public function home()
    {
        $lessors = User::lessors()->count();
        $lessees = User::lessees()->count();

        $team = User::where('admin', 1)->count();

        $propertiesAvailables = Property::available()->count();
        $propertiesUnavailables = Property::unavailable()->count();
        $propertiesTotal = Property::all()->count();

        $contractsPendings = Contract::pending()->count();
        $contractsActives = Contract::active()->count();
        $contractsCanceleds = Contract::canceled()->count();
        $contractsTotal = Contract::all()->count();

        $contracts = Contract::orderBy('id', 'DESC')->limit(10)->get();

        $properties = Property::orderBy('id', 'DESC')->limit(3)->get();

        return view('admin.dashboard', [
            'lessors' => $lessors,
            'lessees' => $lessees,
            'team' => $team,
            'propertiesAvailables' => $propertiesAvailables,
            'propertiesUnavailables' => $propertiesUnavailables,
            'propertiesTotal' => $propertiesTotal,
            'contractsPendings' => $contractsPendings,
            'contractsActives' => $contractsActives,
            'contractsCanceleds' => $contractsCanceleds,
            'contractsTotal' => $contractsTotal,
            'contracts' => $contracts,
            'properties' => $properties

        ]);
    }

    public function login(Request $request)
    {
        if (in_array('', $request->only('email', 'password'))) {
            $json['message'] = $this->message->error('Whooooops!!! Por favor, informe todos os dados corretamente para efetuar o login.')->render();
            return response()->json($json);
        }

        if (!filter_var($request->email, FILTER_VALIDATE_EMAIL)) {
            $json['message'] = $this->message->error('Whooooops!!! Informe um e-mail válido.')->render();
            return response()->json($json);
        }

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        if (!Auth::attempt($credentials)) {
            $json['message'] = $this->message->error('Whooooops!!! Usuário ou senha inválidos.')->render();
            return response()->json($json);
        }

        $this->authenticate($request->getClientIp());

        $json['redirect'] = route('admin.home');
        return response()->json($json);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('admin.login');
    }

    public function authenticate(string $ip)
    {
        $user = User::where('id', Auth::user()->id);
        $user->update([
            'last_login_at' => date('Y-m-d H:i:s'),
            'last_login_ip' => $ip,
        ]);
    }
}
