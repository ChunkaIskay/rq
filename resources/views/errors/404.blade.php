<h2>{{ $exception->getMessage() }}</h2>

<ul class="dropdown-menu">
	<li>
	    <a href="{{ route('logout') }}"
	        onclick="event.preventDefault();
	                 document.getElementById('logout-form1').submit();">
	        Logout
	    </a>

	    <form id="logout-form1" action="{{ route('logout') }}" method="POST" style="display: none;">
	        {{ csrf_field() }}
	    </form>
	</li>
</ul>