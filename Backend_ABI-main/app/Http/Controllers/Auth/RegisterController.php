<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ResearchStaff\ResearchStaffCityProgram;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use App\Models\ResearchStaff\ResearchStaffResearchStaff;
use App\Models\ResearchStaff\ResearchStaffStudent;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

/**
 * Registration controller for research staff user.
 *
 * Handles user registration for different roles (student, professor, committee leader, research_staff)
 * within the research staff system. Each role is associated with a specific profile model.
 * 
 * This controller uses Laravel's RegistersUsers trait but customizes validation, creation,
 * and post-registration behavior to fit the application's requirements.
 */
class RegisterController extends Controller
{
    use RegistersUsers;

    /**
     * Create a new controller instance.
     *
     * Applies middleware to ensure only authenticated users with 'research_staff' role
     * can access registration functionality.
     */
    public function __construct()
    {
        // The middleware is handled in routes, but this inline closure ensures authorization
        $this->middleware(function ($request, $next) {
            if (auth()->check() && auth()->user()->role !== 'research_staff') {
                return redirect('/home')->with('error', 'Unauthorized access');
            }
            return $next($request);
        });
    }

    /**
     * Show the registration form.
     *
     * Loads all city-program combinations and formats them for display in the dropdown.
     * Each program is displayed as "Program Name - City Name".
     */
    public function showRegistrationForm()
    {
        // Load programs for the view
        $cityPrograms = ResearchStaffCityProgram::all();
        foreach ($cityPrograms as $program) {
            $program->full_name = $program->program->name . ' - ' . $program->city->name;
        }

        return view('auth.register', compact('cityPrograms'));
    }

    /**
     * Get a validator instance for the incoming registration request.
     *
     * Validates common fields for all roles and adds role-specific rules:
     * - All roles: name, last_name, phone, password, role, card_id, email
     * - Student/Professor/Committee: city_program_id
     * - Student: semester (1-10)
     *
     * Custom validation ensures card_id is unique across all user types.
     */
    protected function validator(array $data)
    {
    $messages = [
        'name.required' => 'El nombre es obligatorio.',
        'name.string' => 'El nombre debe ser un texto válido.',
        'name.max' => 'El nombre no debe exceder 255 caracteres.',
        
        'last_name.required' => 'El apellido es obligatorio.',
        'last_name.string' => 'El apellido debe ser un texto válido.',
        'last_name.max' => 'El apellido no debe exceder 255 caracteres.',
        
        'phone.required' => 'El teléfono es obligatorio.',
        'phone.string' => 'El teléfono debe ser un texto válido.',
        'phone.max' => 'El teléfono no debe exceder 20 caracteres.',
        
        'password.required' => 'La contraseña es obligatoria.',
        'password.string' => 'La contraseña debe ser un texto válido.',
        'password.min' => 'La contraseña debe tener al menos 8 caracteres.',
        'password.confirmed' => 'Las contraseñas no coinciden.',
        
        'role.required' => 'El rol es obligatorio.',
        'role.in' => 'El rol seleccionado no es válido.',
        
        'card_id.required' => 'La cédula es obligatoria.',
        'card_id.string' => 'La cédula debe ser un texto válido.',
        'card_id.max' => 'La cédula no debe exceder 20 caracteres.',
        
        'email.required' => 'El correo electrónico es obligatorio.',
        'email.email' => 'Debe ingresar un correo electrónico válido.',
        'email.unique' => 'Este correo electrónico ya está registrado.',
        
        'city_program_id.required' => 'El programa es obligatorio.',
        'city_program_id.exists' => 'El programa seleccionado no existe.',
        
        'semester.required' => 'El semestre es obligatorio.',
        'semester.integer' => 'El semestre debe ser un número entero.',
        'semester.min' => 'El semestre mínimo es 1.',
        'semester.max' => 'El semestre máximo es 10.',
    ];

        // Base validation rules common to all roles
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],            
            'password' => ['required', 'string', 'min:4', 'confirmed'],
            'role' => ['required', 'in:student,professor,committee_leader,research_staff'],
            'card_id' => [
                'required',
                'string',
                'max:20',
                function ($attribute, $value, $fail) {
                    $exists = \App\Models\ResearchStaff\ResearchStaffStudent::where('card_id', $value)->exists() ||
                            \App\Models\ResearchStaff\ResearchStaffProfessor::where('card_id', $value)->exists() ||
                            \App\Models\ResearchStaff\ResearchStaffResearchStaff::where('card_id', $value)->exists();

                    if ($exists) {
                        $fail("El número de identificación ya ha sido registrado.");
                    }
                },
            ],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        ];

        // Additional validation based on role
        if (in_array($data['role'], ['student', 'professor', 'committee_leader'])) {
            $rules['city_program_id'] = ['required', 'exists:city_program,id'];
        }

        if ($data['role'] === 'student') {
            $rules['semester'] = ['required', 'integer', 'min:1', 'max:10'];
        }

        return Validator::make($data, $rules, $messages);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * Creates a base user record and then creates a profile record in the appropriate
     * table based on the selected role:
     * - student: ResearchStaffStudent
     * - professor/committee_leader: ResearchStaffProfessor 
     * - research_staff: ResearchStaffResearchStaff
     */
    protected function create(array $data)
    {
        // Create the base user
        $user = ResearchStaffUser::create([
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => $data['role']
        ]);

        // Create role-specific profile
        switch ($data['role']) {
            case 'student':
                $student = new ResearchStaffStudent();
                $student-> card_id = $data['card_id'];
                $student-> name = $data['name'];
                $student-> last_name = $data['last_name'];
                $student-> phone = $data['phone'];
                $student-> semester = $data['semester'];
                $student-> city_program_id = $data['city_program_id'];
                $student-> user_id = $user->id;
                $student-> save();
                break;
                
            case 'professor':
            case 'committee_leader':
                $professor = new ResearchStaffProfessor();
                $professor-> card_id = $data['card_id'];
                $professor-> name = $data['name'];
                $professor-> last_name = $data['last_name'];
                $professor-> phone = $data['phone'];
                $professor->committee_leader = $data['role'] === 'committee_leader' ? 1 : 0;
                $professor-> city_program_id = $data['city_program_id'];
                $professor-> user_id = $user->id;
                $professor-> save();
                break;
                
            case 'research_staff':
                $research_staff = new ResearchStaffResearchStaff();
                $research_staff-> card_id = $data['card_id'];
                $research_staff-> name = $data['name'];
                $research_staff-> last_name = $data['last_name'];
                $research_staff-> phone = $data['phone'];
                $research_staff-> user_id = $user->id;
                $research_staff-> save();
                break;
        }

        return $user;
    }

    /**
     * Handle a registration request for the application.
     *
     * Overrides the default register method to maintain full control over the process.
     * Validates input, creates user and profile, fires Registered event, and handles
     * redirection through the registered() method.
     */
    public function register(Request $request)
    {
        // Validate the data
        $this->validator($request->all())->validate();

        // Create the user (without logging in)
        $user = $this->create($request->all());

        // Fire registration event (optional, for consistency)
        event(new Registered($user));

        // Handle post-registration logic and redirection
        if ($response = $this->registered($request, $user)) {
            return $response;
        }

        // Fallback (should not execute if registered() returns a response)
        return $request->wantsJson()
            ? new JsonResponse([], 201)
            : redirect($this->redirectPath());
    }

    /**
     * Override the post-registration redirect behavior.
     *
     * Instead of redirecting to home, redirects back to the registration form
     * with a success message showing the created user's name.
     */
    protected function registered(Request $request, $user)
    {
        return redirect()->route('register')->with('success', 'Usuario ' . $user->name . ' registrado exitosamente.');
    }
}
