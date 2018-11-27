<?php

namespace App\Http\Controllers\Admin;

use App\User;
use App\Mail\UserCreated;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;


class UserController extends Controller
{
  
  
    public function index(Request $request)
    {

        if (!$request->ajax()){
            return view('admin.user.index');
        }
        $search = $request->search;
        $criteria = $request->criteria;
        
        if ($search==''){
            $users= User::withTrashed()->where('id', '!=', Auth::id())->orderBy('id', 'desc')->paginate(3);
        }
        else{
            $users= User::withTrashed()->where([
                ['id', '!=', Auth::id()],
                [$criteria, 'like', '%'. $search . '%'],
            ])->orderBy('id', 'desc')->paginate(3);
        }
       

        return [
            'pagination' => [
                'total'        => $users->total(),
                'current_page' => $users->currentPage(),
                'per_page'     => $users->perPage(),
                'last_page'    => $users->lastPage(),
                'from'         => $users->firstItem(),
                'to'           => $users->lastItem(),
            ],
            'users' => $users
        ];
    } 
    public function store(Request $request){
        $this->validate($request, [     
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6'],
        ]
        );
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
        //return new UserCreated($request->name,$request->email,$request->password);
        Mail::to($request->email)->send(new UserCreated($request->name,$request->email,$request->password));
        return [];
    }
    public function update(Request $request,$id)
    {
        $user= User::withTrashed()->findOrFail($id);
        $this->validate($request, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            'password' => ['nullable', 'string', 'min:6'],
          ]);
        $user->name = $request->name;
        $user->email = $request->email;
        if($request->password){
            $user->password= Hash::make($request->password);
        }
        $user->save();
        return [];
    
    }
    public function toggle_active(Request $request,$id)
    {
        $user= User::withTrashed()->findOrFail($id);
        if(!$user->active){
            $user->restore();
        }else{
            $user->delete();
        }
        return [];
    
    }
}
