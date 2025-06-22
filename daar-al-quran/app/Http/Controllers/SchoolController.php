<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SchoolController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin', 'approved']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // Only show schools created by the current admin
        $schools = School::where('admin_id', Auth::id())->get();
        return view('admin.schools.index', compact('schools'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.schools.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Check if the admin already has a school
        $existingSchool = School::where('admin_id', Auth::id())->first();
        if ($existingSchool) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'لا يمكنك إنشاء أكثر من مدرسة واحدة');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
            'deletion_code' => 'required|string|min:6',
        ]);

        // Generate a unique code for the school
        $code = Str::random(8);
        while (School::where('code', $code)->exists()) {
            $code = Str::random(8);
        }

        // Ensure deletion_code is properly saved
        $deletionCode = $request->deletion_code;
        if (empty($deletionCode)) {
            $deletionCode = Str::random(8); // Fallback generation if somehow empty
        }

        $school = School::create([
            'name' => $request->name,
            'code' => $code,
            'admin_id' => Auth::id(),
            'address' => $request->address,
            'deletion_code' => $deletionCode,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'تم إنشاء المدرسة بنجاح');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function show(School $school)
    {
        // Make sure the admin owns this school
        if ($school->admin_id !== Auth::id()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'ليس لديك صلاحية الوصول لهذه المدرسة');
        }
        
        return redirect()->route('admin.dashboard');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function edit(School $school)
    {
        // Make sure the admin owns this school
        if ($school->admin_id !== Auth::id()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'ليس لديك صلاحية تعديل هذه المدرسة');
        }
        
        return view('admin.schools.edit', compact('school'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, School $school)
    {
        // Make sure the admin owns this school
        if ($school->admin_id !== Auth::id()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'ليس لديك صلاحية تعديل هذه المدرسة');
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'address' => 'required|string',
        ]);

        $school->update([
            'name' => $request->name,
            'address' => $request->address,
        ]);

        return redirect()->route('admin.dashboard')
            ->with('success', 'تم تعديل المدرسة بنجاح');
    }

    /**
     * Show confirmation form for school deletion
     * 
     * @param \App\Models\School $school
     * @return \Illuminate\Http\Response
     */
    public function confirmDelete(School $school)
    {
        // Check if the authenticated user is the owner of the school
        if ($school->admin_id != Auth::id()) {
            abort(403, 'غير مصرح لك بحذف هذه المدرسة');
        }
        
        return view('admin.schools.confirm-delete', compact('school'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function destroy(School $school)
    {
        // Make sure the admin owns this school
        if ($school->admin_id !== Auth::id()) {
            return redirect()->route('admin.dashboard')
                ->with('error', 'ليس لديك صلاحية حذف هذه المدرسة');
        }
        
        $school->delete();

        return redirect()->route('admin.dashboard')
            ->with('success', 'تم حذف المدرسة بنجاح');
    }

    /**
     * Show the deletion code for a school (debugging only)
     *
     * @param  \App\Models\School  $school
     * @return \Illuminate\Http\Response
     */
    public function showDeletionCode(School $school)
    {
        // Security check
        if ($school->admin_id != Auth::id()) {
            abort(403, 'غير مصرح لك بالوصول إلى هذه المدرسة');
        }
        
        return response()->json([
            'school_id' => $school->id,
            'school_name' => $school->name,
            'deletion_code' => $school->deletion_code,
            'deletion_code_length' => strlen($school->deletion_code),
        ]);
    }
}
