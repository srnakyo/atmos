<div
class="sidebar dark:bg-coal-600 bg-light border-r border-r-gray-200 dark:border-r-coal-100 fixed top-0 bottom-0 z-20 hidden lg:flex flex-col items-stretch shrink-0"
data-drawer="true" data-drawer-class="drawer drawer-start top-0 bottom-0" data-drawer-enable="true|lg:false"
id="sidebar">
<div class="sidebar-header hidden lg:flex items-center relative justify-between px-3 lg:px-6 shrink-0" id="sidebar_header">
	<a class="dark:hidden" href="{{ route('contas') }}">
		<img class="default-logo min-h-[22px] max-w-none" src="assets/media/app/logo-white.png" />
		<img class="small-logo min-h-[22px] max-w-none" src="assets/media/app/mini-logo.png" />
	</a>
	<a class="hidden dark:block" href="{{ route('contas') }}">
		<img class="default-logo min-h-[22px] max-w-none" src="assets/media/app/logo-dark.png" />
		<img class="small-logo min-h-[22px] max-w-none" src="assets/media/app/mini-logo.png" />
	</a>
	<button
	class="btn btn-icon btn-icon-md size-[30px] rounded-lg border border-gray-200 dark:border-gray-300 bg-light text-gray-500 hover:text-gray-700 toggle absolute left-full top-2/4 -translate-x-2/4 -translate-y-2/4"
	data-toggle="body" data-toggle-class="sidebar-collapse" id="sidebar_toggle">
	<i class="ki-filled ki-black-left-line toggle-active:rotate-180 transition-all duration-300"></i>
</button>
</div>

<div class="sidebar-content flex grow shrink-0 py-5 pr-2" id="sidebar_content">
	<div class="scrollable-y-hover grow shrink-0 flex flex-col pl-2 lg:pl-5 pr-1 lg:pr-3" data-scrollable="true"
	data-scrollable-dependencies="#sidebar_header" data-scrollable-height="auto" data-scrollable-offset="0px"
	data-scrollable-wrappers="#sidebar_content" id="sidebar_scrollable">
	
	<div class="menu flex flex-col grow gap-0.5" data-menu="true" data-menu-accordion-expand-all="false" id="sidebar_menu">

		<div class="menu-item">
			<div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px] {{ request()->routeIs('contas') ? 'text-primary' : '' }}"
				tabindex="0" onclick="window.location.href='{{ route('contas') }}';">
				<span class="menu-icon items-start {{ request()->routeIs('contas') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} w-[20px]">
					<i class="ki-outline ki-users text-lg"></i>
				</span>
				<span class="menu-title text-sm font-semibold {{ request()->routeIs('contas') ? 'text-primary' : 'text-gray-700' }}">
					Contas
				</span>
			</div>
		</div>

		<div class="menu-item">
			<div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px] {{ request()->routeIs('cartoes') ? 'text-primary' : '' }}"
				tabindex="0" onclick="window.location.href='{{ route('cartoes') }}';">
				<span class="menu-icon items-start {{ request()->routeIs('cartoes') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} w-[20px]">
					<i class="ki-outline ki-credit-cart text-lg"></i>
				</span>
				<span class="menu-title text-sm font-semibold {{ request()->routeIs('cartoes') ? 'text-primary' : 'text-gray-700' }}">
					Cartões
				</span>
			</div>
		</div>

		<div class="menu-item">
			<div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px] {{ request()->routeIs('monitoramento') ? 'text-primary' : '' }}"
				tabindex="0" onclick="window.location.href='{{ route('monitoramento') }}';">
				<span class="menu-icon items-start {{ request()->routeIs('monitoramento') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} w-[20px]">
					<i class="ki-outline ki-eye text-lg"></i>
				</span>
				<span class="menu-title text-sm font-semibold {{ request()->routeIs('monitoramento') ? 'text-primary' : 'text-gray-700' }}">
					Monitoramento
				</span>
			</div>
		</div>

		<div class="menu-item">
			<div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px] {{ request()->routeIs('registro_compras') ? 'text-primary' : '' }}"
				tabindex="0" onclick="window.location.href='{{ route('registro_compras') }}';">
				<span class="menu-icon items-start {{ request()->routeIs('registro_compras') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} w-[20px]">
					<i class="ki-outline ki-purchase text-lg"></i>
				</span>
				<span class="menu-title text-sm font-semibold {{ request()->routeIs('registro_compras') ? 'text-primary' : 'text-gray-700' }}">
					Histórico de Compras
				</span>
			</div>
		</div>

		<div class="menu-item">
			<div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px] {{ request()->routeIs('drop') ? 'text-primary' : '' }}"
				tabindex="0" onclick="window.location.href='{{ route('drop') }}';">
				<span class="menu-icon items-start {{ request()->routeIs('drop') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} w-[20px]">
					<i class="ki-outline ki-rocket text-lg"></i>
				</span>
				<span class="menu-title text-sm font-semibold {{ request()->routeIs('drop') ? 'text-primary' : 'text-gray-700' }}">
					Drop
				</span>
			</div>
		</div>

		<div class="menu-item">
			<div class="menu-link flex items-center grow cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px] {{ request()->routeIs('drop_pro') ? 'text-primary' : '' }}"
				tabindex="0" onclick="window.location.href='{{ route('drop_pro') }}';">
				<span class="menu-icon items-start {{ request()->routeIs('drop_pro') ? 'text-primary' : 'text-gray-500 dark:text-gray-400' }} w-[20px]">
					<i class="ki-outline ki-rocket text-lg"></i>
				</span>
				<span class="menu-title text-sm font-semibold {{ request()->routeIs('drop_pro') ? 'text-primary' : 'text-gray-700' }}">
					Drop <img src="{{ asset('assets/media/pro-ico-v3.svg') }}" alt="Pro Icon" class="w-12 h-12 ml-2">
				</span>
			</div>
		</div>


<!-- Formulário escondido para logout -->
<form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
	@csrf
</form>

<div class="grow"></div>

<div class="menu-item">
	<div onclick="document.getElementById('logout-form').submit();" class="menu-link flex items-center w-full cursor-pointer border border-transparent gap-[10px] pl-[10px] pr-[10px] py-[6px] text-left">
		<span class="menu-icon items-start text-gray-500 dark:text-gray-400 w-[20px]">
			<i class="ki-outline ki-entrance-left text-lg"></i>
		</span>
		<span class="menu-title text-sm font-semibold text-gray-700 menu-item-active:text-primary menu-link-hover:!text-primary">
			Desconectar
		</span>
	</div>
</div>



</div>
</div>
</div>
</div>
