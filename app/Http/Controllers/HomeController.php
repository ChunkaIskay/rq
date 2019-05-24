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
        $menu = DB::select('SELECT rs.menu_id, rs.submenu_id, rs.name, rs.url, rs.level, rs.rol_name,
                             CASE
                                WHEN rs.submenu_id = 0 THEN rs.name
                               
                             END as task
                            FROM (      SELECT menu.menu_id, menu.submenu_id, menu.name, menu.url, menu.level, roles.name rol_name
                                        FROM `menu_role` 
                                        INNER JOIN menu on (menu_role.menu_id=menu.menu_id and menu.disable = 0)
                                        INNER JOIN roles on (menu_role.role_id = roles.id and menu_role.disable = 0)
                                        WHERE menu_role.role_id = :id
                                        ORDER BY menu_role.menu_id, menu.submenu_id 
                                        ASC
                    )rs', ['id' => $role->role_id]);
        $fullName = $request->user()->name." ".$request->user()->ap_paterno;
        $cuentaUsuario = $request->user()->cuenta_usuario;

    //   dd($menu);
/*    +"menu_id": 1
    +"submenu_id": 0
    +"name": "Tareas Importantes"
    +"url": "tareas_importantes"
    +"level": 0
    +"rol_name": "Jefe de Sistemas"
*/
        return view('home')->with(compact('menu','fullName','cuentaUsuario'));
        
    }
}
