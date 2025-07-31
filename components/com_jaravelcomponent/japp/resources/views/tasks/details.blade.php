<div class="container-fluid">
    <h4>Task Details</h4>
    @if($task)
        <table class="table table-striped">
            <tr><td><strong>ID</strong></td><td>{{ $task->id }}</td></tr>
            <tr><td><strong>Title</strong></td><td>{{ $task->title }}</td></tr>
            <tr><td><strong>Description</strong></td><td>{{ $task->description }}</td></tr>
            <tr><td><strong>Long Description</strong></td><td>{{ $task->long_description ?? 'N/A' }}</td></tr>
            <tr><td><strong>Completed</strong></td><td>{{ $task->completed ? 'Yes' : 'No' }}</td></tr>
            <tr><td><strong>Created</strong></td><td>{{ $task->created_at }}</td></tr>
            <tr><td><strong>Updated</strong></td><td>{{ $task->updated_at }}</td></tr>
        </table>
    @else
        <div class="alert alert-warning">Task not found.</div>
    @endif

    <a href="{{ $jurl('tasks') }}" class="btn btn-secondary">Back to Tasks</a>
</div>