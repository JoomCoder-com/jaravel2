<div class="container-fluid">
    <h3>Dashboard</h3>

    <div class="row mb-3">
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title">{{ $totalTasks }}</h5>
                    <p class="card-text">Total Tasks</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-success">{{ $completedTasks }}</h5>
                    <p class="card-text">Completed</p>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-center">
                <div class="card-body">
                    <h5 class="card-title text-warning">{{ $pendingTasks }}</h5>
                    <p class="card-text">Pending</p>
                </div>
            </div>
        </div>
    </div>

    <a href="{{ $jurl('tasks') }}" class="btn btn-primary">Manage Tasks</a>
    <a href="{{ $jurl('admin/stats') }}" class="btn btn-outline-secondary">Admin Stats</a>
</div>