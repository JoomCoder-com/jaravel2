<div class="container-fluid">
	<h3>All Tasks ({{ $total }})</h3>

	@if(session('message'))
	<div class="alert alert-success">{{ session('message') }}</div>
	@endif

	<div class="row">
		@foreach($tasks as $task)
		<div class="col-md-6 mb-3">
			<div class="card">
				<div class="card-body">
					<h5 class="card-title">{{ $task->title }}</h5>
					<p class="card-text">{{ $task->description }}</p>
					<span class="badge {{ $task->completed ? 'bg-success' : 'bg-warning' }}">
                            {{ $task->completed ? 'Completed' : 'Pending' }}
                        </span>
					<a href="{{ $jurl('tasks/' . $task->id) }}" class="btn btn-sm btn-outline-primary">View</a>
				</div>
			</div>
		</div>
		@endforeach
	</div>
</div>