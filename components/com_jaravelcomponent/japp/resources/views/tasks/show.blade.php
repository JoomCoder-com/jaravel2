<div class="container-fluid">
    <h3>{{ $task->title }}</h3>
    <p><strong>Description:</strong> {{ $task->description }}</p>

    @if($task->long_description)
        <p><strong>Details:</strong> {{ $task->long_description }}</p>
    @endif

    <p><strong>Status:</strong>
        <span class="badge {{ $task->completed ? 'bg-success' : 'bg-warning' }}">
            {{ $task->completed ? 'Completed' : 'Pending' }}
        </span>
    </p>

    <p><strong>Created:</strong> {{ $task->created_at }}</p>

    <a href="{{ $jurl('tasks') }}" class="btn btn-secondary">Back to Tasks</a>
    <a href="{{ $jurl('tasks/' . $task->id . '/details') }}" class="btn btn-info">View Details</a>
</div>