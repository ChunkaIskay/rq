<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;


class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     */

    /*
    public function index()
    {
        return view('home');
    }
    */

    public function index(Request $request)
    {   
        $rol = "desconocido";
   
         $role = DB::table('role_user')
            ->join('roles', 'role_user.role_id', '=' , 'roles.id')
            ->select('role_user.role_id as role_id','role_user.user_id as user_id', 'roles.name as role') 
            ->where( 'role_user.user_id',$request->user()->id)
            ->first();
     
       if(!empty($role->role)){
                $rol = $role->role;
       }

        $request->user()->authorizeRoles([$request->user()->name, $rol]);
   
        return view('home');
    }
}
