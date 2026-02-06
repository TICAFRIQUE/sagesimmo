<?php

namespace App\Http\Controllers\frontend;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        // Sauvegarder l'URL précédente si elle existe et n'est pas login/register
        $previous = url()->previous();
        $current = url()->current();

        if (
            $previous !== $current &&
            !str_contains($previous, '/connexion') &&
            !str_contains($previous, '/inscription')
        ) {
            session(['url.intended' => $previous]);
        }
        return view('frontend.pages.auth.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required',
        ]);

        // Déterminer si c'est un email ou un téléphone
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'phone';

        $credentials = [
            $loginType => $request->login,
            'password' => $request->password,
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = User::find(Auth::id());
            // Rediriger vers le dashboard backend si admin
            if ($user->hasRole('admin')) {
                return redirect()->intended(route('backend.dashboard'));
            }

            // Sinon rediriger vers l'accueil
            return redirect()->intended(route('home'))->with('success', 'Connexion réussie !');
        }

        return back()->withErrors([
            'login' => 'Les identifiants fournis ne correspondent pas à nos enregistrements.',
        ])->onlyInput('login');
    }

    /**
     * Afficher le formulaire d'inscription
     */
    public function showRegisterForm()
    {

        // Sauvegarder l'URL précédente si elle existe et n'est pas login/register
        $previous = url()->previous();
        $current = url()->current();

        if (
            $previous !== $current &&
            !str_contains($previous, '/connexion') &&
            !str_contains($previous, '/inscription')
        ) {
            session(['url.intended' => $previous]);
        }
        // Récupérer uniquement les rôles pour les utilisateurs frontend
        $roles = Role::whereIn('name', ['locataire', 'proprietaire', 'acheteur'])->get();
        return view('frontend.pages.auth.register', compact('roles'));
    }

    /**
     * Traiter l'inscription
     */
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|unique:users,phone',
            'password' => 'required|string|min:6|confirmed',
            'role_id' => 'required|exists:roles,id',
            'g-recaptcha-response' => 'required',
        ], [
            'g-recaptcha-response.required' => 'Veuillez cocher la case "Je ne suis pas un robot".',
        ]);

        // Vérifier le reCAPTCHA
        $recaptchaSecret = config('services.recaptcha.secret_key');
        $recaptchaResponse = $request->input('g-recaptcha-response');

        $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret={$recaptchaSecret}&response={$recaptchaResponse}");
        $responseKeys = json_decode($response, true);

        if (!$responseKeys["success"]) {
            return back()->withErrors([
                'g-recaptcha-response' => 'La vérification reCAPTCHA a échoué. Veuillez réessayer.'
            ])->withInput();
        }

        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => Hash::make($request->password),
        ]);

        // Assigner le rôle
        $role = Role::findById($request->role_id);
        $user->assignRole($role);

        // Connecter automatiquement l'utilisateur
        Auth::login($user);


        //retourner vers la page avant la page login ou home
        return redirect()->intended(route('home'))->with('success', 'Inscription réussie ! Bienvenue sur notre plateforme.');
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        

        return redirect()->route('home')->with('success', 'Déconnexion réussie.');
    }
}
