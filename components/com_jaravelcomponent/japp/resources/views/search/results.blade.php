<div class="container-fluid">
    <h4>Search Results</h4>

    @if($query)
        <p>Search query: <strong>"{{ $query }}"</strong></p>
    @endif

    <p>Found {{ $total }} result(s)</p>

    @if($results->count() > 0)
        <div class="row">
            @foreach($results as $task)
                <div class="col-md-6 mb-2">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title">{{ $task->title }}</h6>
                            <p class="card-text small">{{ $task->description }}</p>
                            <span class="badge {{ $task->completed ? 'bg-success' : 'bg-warning' }} badge-sm">
                                {{ $task->completed ? 'Done' : 'Pending' }}
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="alert alert-info">No tasks found matching your criteria.</div>
    @endif

    <a href="{{ $jurl('tasks') }}" class="btn btn-secondary">All Tasks</a>
</div>