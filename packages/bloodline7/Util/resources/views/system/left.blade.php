<div class="col mb-3 col-md-2">

	<div class="list-group" id="sideMenu">

		<a href="/{{ config('admin.prefix') }}/system/adminList" class="list-group-item list-group-item-action active"><i class="bi bi-x-diamond-fill"></i> Administrators</a>
<!--		<a href="/{{ config('admin.prefix') }}/system/codeManager/list" class="list-group-item list-group-item-action"><i class="bi bi-archive"></i> Code Manager</a>-->

		<a class="list-group-item list-group-item-action" data-bs-toggle="collapse" href="#routers" role="button" aria-expanded="false" aria-controls="collapseExample">
			<i class="bi bi-diagram-3-fill"></i> Routers
		</a>

		<div class="collapse" id="routers">
			<a href="/{{ config('admin.prefix') }}/system/router" class="list-group-item list-group-item-action"><i class="bi bi-diagram-3-fill"></i> Router for Admin</a>
<!--			<a href="/{{ config('admin.prefix') }}/system/router-f" class="list-group-item list-group-item-action"><i class="bi bi-diagram-3-fill"></i> Router for Front</a>-->
		</div>

		{{--<a href="/{{ config('admin.prefix') }}/system/router-content" class="list-group-item list-group-item-action"><i class="bi bi-credit-card-2-front"></i> Content Manager</a>--}}
		{{--<a href="/{{ config('admin.prefix') }}/system/floorplan" class="list-group-item list-group-item-action"><i class="bi bi-credit-card-2-front"></i> FloorPlan</a>--}}

<!--		<a class="list-group-item list-group-item-action" data-bs-toggle="collapse" href="#setting" role="button" aria-expanded="false" aria-controls="collapseExample">
			<i class="bi bi-gear"></i> Setting</a>
		</a>

		<div class="collapse" id="setting">
			<a href="/{{ config('admin.prefix') }}/system/store" class="list-group-item list-group-item-action"><i class="fas fa-store-alt"></i> Store</a>

			<a href="/{{ config('admin.prefix') }}/system/category" class="list-group-item list-group-item-action"><i class="fas fa-folder"></i> Category</a>

			<a href="/{{ config('admin.prefix') }}/system/template" class="list-group-item list-group-item-action"><i class="fas fa-palette"></i> Template</a>
			<a href="/{{ config('admin.prefix') }}/system/smtp" class="list-group-item list-group-item-action"><i class="fas fa-envelope"></i> SMTP</a>
		</div>-->
	</div>
</div>

