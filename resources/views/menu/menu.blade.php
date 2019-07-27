<!-- Nav Item - Pages Collapse Menu -->
    
<li class="nav-item">

  
  @foreach(getMenuRol() as $key => $menulist)
     @if( $menulist->submenu_id == 0 &&  $menulist->level == 0 )
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapsePages_{{ $key }}" aria-expanded="true" aria-controls="collapsePages">
          <i class="fas fa-fw fa-folder"></i>
          <span>{{ $menulist->name }}</span>
        </a>
          
          <div id="collapsePages_{{ $key }}" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
          <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Tareas del {{ $menulist->rol_name }}</h6>
              @foreach(getMenuRol() as $key1 => $menulist1)
                 @if( $menulist->menu_id == $menulist1->submenu_id &&  $menulist1->level == 1 )
                        <a class="collapse-item" href="{{ url($menulist1->desc_short.'/'.$menulist1->url) }}">{{ $menulist1->name }}</a>  
                @endif
              @endforeach
           </div>
        </div>
    @endif
  @endforeach
</li>
