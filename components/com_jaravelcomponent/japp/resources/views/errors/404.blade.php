<div class="container-fluid">
    <div class="alert alert-danger">
        <h4>Page Not Found</h4>
        <p>{{ $message }}</p>
    </div>

    <h5>Available Routes:</h5>
    <ul class="list-group">
        @foreach($availableRoutes as $route => $description)
            <li class="list-group-item d-flex justify-content-between">
                <span><code>{{ $route }}</code></span>
                <span>{{ $description }}</span>
            </li>
        @endforeach
    </ul>

    <div class="mt-3">
        <a href="{{ $jurl('') }}" class="btn btn-primary">Go Home</a>
    </div>
</div>