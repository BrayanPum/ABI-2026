<?php

namespace App\Http\Controllers;

use App\Models\ResearchStaff\ResearchStaffCityProgram;
use App\Models\ResearchStaff\ResearchStaffProfessor;
use App\Models\ResearchStaff\ResearchStaffResearchStaff;
use App\Models\ResearchStaff\ResearchStaffStudent;
use App\Models\ResearchStaff\ResearchStaffUser;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        // Get filter and search parameters
        $search = $request->get('search');
        $role = $request->get('role');
        $state = $request->get('state');
        if ($state !== null) {
            $state = (int)$state;
        }
        $cityProgramId = $request->get('city_program_id');
        $perPageOptions = [10, 20, 30];
        $perPage = (int) $request->get('per_page', $perPageOptions[0]);

        if (!in_array($perPage, $perPageOptions, true)) {
            $perPage = $perPageOptions[0];
        }

        // Query base
        $query = ResearchStaffUser::query();

        // Apply filters
        if ($role) {
            $query->where('role', $role);
        }

        if ($state !== null) {
            $query->where('state', $state);
        }

        if ($cityProgramId) {
            $studentIds = ResearchStaffStudent::where('city_program_id', $cityProgramId)->pluck('user_id');
            $professorIds = ResearchStaffProfessor::where('city_program_id', $cityProgramId)->pluck('user_id');
            
            $query->where(function ($q) use ($studentIds, $professorIds) {
                $q->whereIn('id', $studentIds)
                    ->orWhereIn('id', $professorIds);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('email', 'like', '%' . $search . '%');
                
                if (is_numeric($search)) {
                    $q->orWhere('id', $search);
                }
                
                $searchTerm = '%' . $search . '%';
                
                $studentIds = ResearchStaffStudent::where('name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('card_id', 'like', $searchTerm)
                    ->pluck('user_id');
                    
                $professorIds = ResearchStaffProfessor::where('name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('card_id', 'like', $searchTerm)
                    ->pluck('user_id');
                    
                $researchStaffIds = ResearchStaffResearchStaff::where('name', 'like', $searchTerm)
                    ->orWhere('last_name', 'like', $searchTerm)
                    ->orWhere('card_id', 'like', $searchTerm)
                    ->pluck('user_id');
                    
                $q->orWhereIn('id', $studentIds)
                    ->orWhereIn('id', $professorIds)
                    ->orWhereIn('id', $researchStaffIds);
            });
        }

        
        // Only order by created_at in the SQL query
        $query->orderBy('created_at', 'desc');

        // Get paginated users
        $users = $query->paginate($perPage);
        $users->appends($request->query());

        // SORT BY NAME IN PHP (after getting the results)
        $usersCollection = $users->getCollection()->sortBy(function ($user) {
            switch ($user->role) {
                case 'student':
                    return $user->details->name ?? '';
                case 'professor':
                case 'committee_leader':
                    return $user->details->name ?? '';
                case 'research_staff':
                    return $user->details->name ?? '';
                default:
                    return '';
            }
        });

        // Replace paginated collection with sorted collection
        $users->setCollection($usersCollection);

        // Upload user details
        $userIds = collect($users->items())->pluck('id')->toArray();
        $studentIds = [];
        $professorIds = [];
        $researchStaffIds = [];

        foreach ($users as $user) {
            switch ($user->role) {
                case 'student':
                    $studentIds[] = $user->id;
                    break;
                case 'professor':
                case 'committee_leader':
                    $professorIds[] = $user->id;
                    break;
                case 'research_staff':
                    $researchStaffIds[] = $user->id;
                    break;
            }
        }

        $students = ResearchStaffStudent::whereIn('user_id', $studentIds)->get()->keyBy('user_id');
        $professors = ResearchStaffProfessor::whereIn('user_id', $professorIds)->get()->keyBy('user_id');
        $researchStaffs = ResearchStaffResearchStaff::whereIn('user_id', $researchStaffIds)->get()->keyBy('user_id');

        foreach ($users as $user) {
            switch ($user->role) {
                case 'student':
                    $user->details = $students[$user->id] ?? null;
                    break;
                case 'professor':
                case 'committee_leader':
                    $user->details = $professors[$user->id] ?? null;
                    break;
                case 'research_staff':
                    $user->details = $researchStaffs[$user->id] ?? null;
                    break;
            }
        }

        // Load programs
        $cityPrograms = ResearchStaffCityProgram::all();
        foreach ($cityPrograms as $program) {
            $program->full_name = $program->program->name . ' - ' . $program->city->name;
        }

        return view('users.index', [
            'users' => $users,
            'search' => $search,
            'role' => $role,
            'state' => $state,
            'cityProgramId' => $cityProgramId,
            'cityPrograms' => $cityPrograms,
            'perPage' => $perPage,
            'perPageOptions' => $perPageOptions,
            'i' => ($users->currentPage() - 1) * $users->perPage(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(ResearchStaffUser $user): View
    {
        
        // Load additional data according to the role
        $details = null;
        
        switch ($user->role) {
            case 'student':
                // Load relationships needed for city_program
                $details = ResearchStaffStudent::with([
                    'cityProgram' => function($query) {
                        $query->with(['city', 'program']);
                    }
                ])->where('user_id', $user->id)->first();
                break;
                
            case 'professor':
            case 'committee_leader':
                // Load relationships needed for city_program
                $details = ResearchStaffProfessor::with([
                    'cityProgram' => function($query) {
                        $query->with(['city', 'program']);
                    }
                ])->where('user_id', $user->id)->first();
                break;
                
            case 'research_staff':
                $details = ResearchStaffResearchStaff::where('user_id', $user->id)->first();
                break;
        }
        
        // Pass both the user and their details to the view
        return view('users.show', compact('user', 'details'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ResearchStaffUser $user): \Illuminate\View\View
    {
        // Load additional data according to the role
        $details = null;
        $cityPrograms = ResearchStaffCityProgram::all();
        
        foreach ($cityPrograms as $program) {
            $program->full_name = $program->program->name . ' - ' . $program->city->name;
        }

        switch ($user->role) {
            case 'student':
                $details = ResearchStaffStudent::where('user_id', $user->id)->first();
                break;
                
            case 'professor':
            case 'committee_leader':
                $details = ResearchStaffProfessor::where('user_id', $user->id)->first();
                break;
                
            case 'research_staff':
                $details = ResearchStaffResearchStaff::where('user_id', $user->id)->first();
                break;
        }

        return view('users.edit', [
            'user' => $user,
            'details' => $details,
            'cityPrograms' => $cityPrograms,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(\Illuminate\Http\Request $request, ResearchStaffUser $user): RedirectResponse
    {
        // Validate common fields
        $validated = $request->validate([
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id)
            ],
            'state' => 'required|in:1,0',
            'role' => 'required|in:student,professor,committee_leader,research_staff',
            'password' => 'nullable|min:8|confirmed',
        ]);

        $newRole = $request->role;
        $oldRole = $user->role;
        
        $additional = $this->validateRoleFields($request, $newRole);

        // Update the user
        $user->update([
            'email' => $validated['email'],
            'state' => $validated['state'],
            'role' => $newRole,
            'password' => $validated['password'] ? Hash::make($validated['password']) : $user->password,
        ]);


        $isTeacherRoleChange = 
            ($oldRole === 'professor' && $newRole === 'committee_leader') ||
            ($oldRole === 'committee_leader' && $newRole === 'professor');

        // If there is a role change, we need to manage the information from the role tables
    
        if ($newRole !== $oldRole && !$isTeacherRoleChange) {
            // Delete (soft delete) the record from the previous table
            $this->deleteOldRoleRecord($user, $oldRole);
            
            // Create a new record in the new role's table
            $this->createNewRoleRecord($user, $newRole, $additional);
        } 
        // If the role does not change, we update the existing record
        else {
            $this->updateExistingRoleRecord($user, $newRole, $additional);
        }

        return redirect()
            ->route('users.index')
            ->with('success', "Usuario '{$user->email}' actualizado correctamente.");
    }

    /**
     * Validates specific fields based on role
     */
    private function validateRoleFields(\Illuminate\Http\Request $request, string $role): array
    {
        switch ($role) {
            case 'student':
                return $request->validate([
                    'card_id' => 'required|string|max:20',
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'semester' => 'required|integer|min:1|max:10',
                    'city_program_id' => 'required|exists:city_program,id',
                ]);
                
            case 'professor':
            case 'committee_leader':
                $validated = $request->validate([
                    'card_id' => 'required|string|max:20',
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                    'city_program_id' => 'required|exists:city_program,id',
                    'committee_leader' => 'required|in:0,1',
                ]);
                
                // We ignore what the client sent and set based on the role
                $validated['committee_leader'] = ($role === 'committee_leader') ? 1 : 0;
                return $validated;
            case 'research_staff':
                return $request->validate([
                    'card_id' => 'required|string|max:20',
                    'name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'phone' => 'required|string|max:20',
                ]);
                
            default:
                throw new \Exception('Rol no válido');
        }
    }

    /**
     * Deletes the record from the table of the previous role
     */
    private function deleteOldRoleRecord(ResearchStaffUser $user, string $role): void
    {
        switch ($role) {
            case 'student':
                ResearchStaffStudent::where('user_id', $user->id)->delete();
                break;
                
            case 'professor':
            case 'committee_leader':
                ResearchStaffProfessor::where('user_id', $user->id)->delete();
                break;
                
            case 'research_staff':
                ResearchStaffResearchStaff::where('user_id', $user->id)->delete();
                break;
        }
    }

    /**
     * Create a new record in the new role's table or restore an existing one
     */
    private function createNewRoleRecord(ResearchStaffUser $user, string $role, array $data): void
    {
        switch ($role) {
            case 'student':
                // Check if a student with this ID already exists
                $existingStudent = ResearchStaffStudent::withTrashed()
                    ->where('card_id', $data['card_id'])
                    ->first();
                
                if ($existingStudent) {
                    // If it exists and is deleted, restore it and update
                    if ($existingStudent->trashed()) {
                        $existingStudent->restore();
                        $existingStudent->update([
                            'name' => $data['name'],
                            'last_name' => $data['last_name'],
                            'phone' => $data['phone'],
                            'semester' => $data['semester'],
                            'city_program_id' => $data['city_program_id'],
                            'user_id' => $user->id
                        ]);
                    } 
                    // If it exists and is not deleted, check if it is the same user
                    else {
                        // If it is the same user (same user_id), just update
                        if ($existingStudent->user_id == $user->id) {
                            $existingStudent->update([
                                'name' => $data['name'],
                                'last_name' => $data['last_name'],
                                'phone' => $data['phone'],
                                'semester' => $data['semester'],
                                'city_program_id' => $data['city_program_id']
                            ]);
                        } 
                        // If it is a different user, throw an exception (duplicate ID)
                        else {
                            throw new \Exception('Ya existe un estudiante con esta cédula');
                        }
                    }
                } 
                // If it doesn't exist, create a new one
                else {
                    ResearchStaffStudent::create([
                        'card_id' => $data['card_id'],
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'semester' => $data['semester'],
                        'city_program_id' => $data['city_program_id'],
                        'user_id' => $user->id,
                    ]);
                }
                break;
                
            case 'professor':
            case 'committee_leader':
                // Check if a teacher with this ID already exists
                $existingProfessor = ResearchStaffProfessor::withTrashed()
                    ->where('card_id', $data['card_id'])
                    ->first();
                
                if ($existingProfessor) {
                    // If it exists and is deleted, restore it and update
                    if ($existingProfessor->trashed()) {
                        $existingProfessor->restore();
                        $existingProfessor->update([
                            'name' => $data['name'],
                            'last_name' => $data['last_name'],
                            'phone' => $data['phone'],
                            'committee_leader' => ($role === 'committee_leader') ? 1 : 0,
                            'city_program_id' => $data['city_program_id'],
                            'user_id' => $user->id
                        ]);
                    } 
                    // If it exists and is not deleted, check if it is the same user
                    else {
                        // If it is the same user (same user_id), just update
                        if ($existingProfessor->user_id == $user->id) {
                            $existingProfessor->update([
                                'name' => $data['name'],
                                'last_name' => $data['last_name'],
                                'phone' => $data['phone'],
                                'committee_leader' => $data['committee_leader'] ?? ($role === 'committee_leader' ? 1 : 0),
                                'city_program_id' => $data['city_program_id']
                            ]);
                        } 
                        // If it is a different user, throw an exception (duplicate ID)
                        else {
                            throw new \Exception('Ya existe un profesor con esta cédula');
                        }
                    }
                } 
                // If it doesn't exist, create a new one
                else {
                    ResearchStaffProfessor::create([
                        'card_id' => $data['card_id'],
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'committee_leader' => $data['committee_leader'] ?? ($role === 'committee_leader' ? 1 : 0),
                        'city_program_id' => $data['city_program_id'],
                        'user_id' => $user->id,
                    ]);
                }
                break;
                
            case 'research_staff':
                // Check if a staff member with this ID already exists
                $existingResearchStaff = ResearchStaffResearchStaff::withTrashed()
                    ->where('card_id', $data['card_id'])
                    ->first();
                
                if ($existingResearchStaff) {
                    // If it exists and is deleted, restore it and update
                    if ($existingResearchStaff->trashed()) {
                        $existingResearchStaff->restore();
                        $existingResearchStaff->update([
                            'name' => $data['name'],
                            'last_name' => $data['last_name'],
                            'phone' => $data['phone'],
                            'user_id' => $user->id
                        ]);
                    } 
                    // If it exists and is not deleted, check if it is the same user
                    else {
                        // If it is the same user (same user_id), just update
                        if ($existingResearchStaff->user_id == $user->id) {
                            $existingResearchStaff->update([
                                'name' => $data['name'],
                                'last_name' => $data['last_name'],
                                'phone' => $data['phone']
                            ]);
                        } 
                        // If it is a different user, throw an exception (duplicate ID)
                        else {
                            throw new \Exception('Ya existe un miembro de staff con esta cédula');
                        }
                    }
                } 
                // If it doesn't exist, create a new one
                else {
                    ResearchStaffResearchStaff::create([
                        'card_id' => $data['card_id'],
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'user_id' => $user->id,
                    ]);
                }
                break;
        }
    }

    /**
     * Updates the existing record in the role table
     */
    private function updateExistingRoleRecord(ResearchStaffUser $user, string $role, array $data): void
    {
        switch ($role) {
            case 'student':
                $student = ResearchStaffStudent::where('user_id', $user->id)->first();
                if ($student) {
                    $student->update([
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'semester' => $data['semester'],
                        'city_program_id' => $data['city_program_id'],
                    ]);
                }
                break;
                
            case 'professor':
            case 'committee_leader':
                $professor = ResearchStaffProfessor::where('user_id', $user->id)->first();
                if ($professor) {
                    $professor->update([
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                        'committee_leader' => ($role === 'committee_leader') ? 1 : 0,
                        'city_program_id' => $data['city_program_id'],
                    ]);
                }
                break;
                
            case 'research_staff':
                $researchStaff = ResearchStaffResearchStaff::where('user_id', $user->id)->first();
                if ($researchStaff) {
                    $researchStaff->update([
                        'name' => $data['name'],
                        'last_name' => $data['last_name'],
                        'phone' => $data['phone'],
                    ]);
                }
                break;
        }
    }

    /**
     * Inactivate the specified user
     */
    public function destroy(ResearchStaffUser $user): RedirectResponse
    {
        $user->state = 0;
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', "Usuario '{$user->email}' ha sido desactivado correctamente.");
    }

    /**
     * Reactivate the specified user
     */
    public function activate(ResearchStaffUser $user): RedirectResponse
    {
        $user->state = 1;
        $user->save();

        return redirect()
            ->route('users.index')
            ->with('success', "Usuario '{$user->email}' ha sido reactivado correctamente.");
    }
}
