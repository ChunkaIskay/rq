<?php

function getMenuRol()
{  
    $rol = "desconocido";

    $role = DB::table('role_user')
        ->join('roles', 'role_user.role_id', '=' , 'roles.id')
        ->select('role_user.role_id as role_id','role_user.user_id as user_id', 'roles.name as role') 
        ->where( 'role_user.user_id',auth()->user()->id)
        ->first();

    if(!empty($role->role)){
        $rol = $role->role;
    }

    auth()->user()->authorizeRoles([auth()->user()->name, $rol]);
    $menu = DB::select('SELECT rs.menu_id, rs.submenu_id, rs.name, rs.url, rs.level, rs.rol_name, rs.desc_short,
                         CASE
                            WHEN rs.submenu_id = 0 THEN rs.name
                           
                         END as task
                        FROM (      SELECT menu.menu_id, menu.submenu_id, menu.name, menu.url, menu.level, roles.name rol_name, roles.desc_short desc_short
                                    FROM `menu_role` 
                                    INNER JOIN menu on (menu_role.menu_id=menu.menu_id and menu.disable = 0)
                                    INNER JOIN roles on (menu_role.role_id = roles.id and menu_role.disable = 0)
                                    WHERE menu_role.role_id = :id
                                    ORDER BY menu_role.menu_id, menu.submenu_id 
                                    ASC
                )rs', ['id' => $role->role_id]);
    //  $fullName = auth()->user()->name." ".auth()->user()->ap_paterno;
    //  $cuentaUsuario = auth()->user()->cuenta_usuario;
    //dd($menu);
	//return view('home')->with(compact('menu','fullName','cuentaUsuario'));
    return $menu;
        
}