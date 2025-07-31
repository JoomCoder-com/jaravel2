<?php

use Illuminate\Support\Facades\Route;

class Task
{
	public function __construct(
		public int $id,
		public string $title,
		public string $description,
		public ?string $long_description,
		public bool $completed,
		public string $created_at,
		public string $updated_at
	) {
	}
}

$tasks = [
	new Task(
		1,
		'Buy groceries',
		'Task 1 description',
		'Task 1 long description',
		false,
		'2023-03-01 12:00:00',
		'2023-03-01 12:00:00'
	),
	new Task(
		2,
		'Sell old stuff',
		'Task 2 description',
		null,
		false,
		'2023-03-02 12:00:00',
		'2023-03-02 12:00:00'
	),
	new Task(
		3,
		'Learn programming',
		'Task 3 description',
		'Task 3 long description',
		true,
		'2023-03-03 12:00:00',
		'2023-03-03 12:00:00'
	),
	new Task(
		4,
		'Take dogs for a walk',
		'Task 4 description',
		null,
		false,
		'2023-03-04 12:00:00',
		'2023-03-04 12:00:00'
	),
];

// 1. Basic GET route with view (existing)
Route::get('/', function () {
	return view('index', [
		'name' => 'Piotr'
	]);
});

// 2. Simple GET route with tasks view
Route::get('/tasks', function () use ($tasks) {
	return view('tasks.index', [
		'tasks' => $tasks,
		'total' => count($tasks)
	]);
});

// 3. Route with required parameter - show single task
Route::get('/tasks/{id}', function ($id) use ($tasks) {
	$task = collect($tasks)->firstWhere('id', (int) $id);

	if (!$task) {
		abort(404, 'Task not found');
	}

	return view('tasks.show', ['task' => $task]);
})->where('id', '[0-9]+');

// 4. Route with optional parameter - task details or form
Route::get('/tasks/{id?}/details', function ($id = null) use ($tasks) {
	if ($id) {
		$task = collect($tasks)->firstWhere('id', (int) $id);
		return view('tasks.details', ['task' => $task]);
	}

	return view('tasks.select', ['message' => 'Please select a task to view details']);
})->where('id', '[0-9]+');

// 5. POST route with redirect and flash message
Route::post('/tasks', function (\Illuminate\Http\Request $request) {
	// Simulate task creation
	session()->flash('success', 'Task created successfully!');
	session()->flash('task_data', $request->all());

	return redirect('/tasks')->with('message', 'New task added');
});

// 6. PUT route with redirect back
Route::put('/tasks/{id}', function (\Illuminate\Http\Request $request, $id) {
	session()->flash('success', "Task {$id} updated successfully!");
	session()->flash('updated_data', $request->all());

	return redirect()->back()->with('message', "Task {$id} has been updated");
})->where('id', '[0-9]+');

// 7. DELETE route with redirect
Route::delete('/tasks/{id}', function ($id) {
	session()->flash('success', "Task {$id} deleted successfully!");

	return redirect('/tasks')->with('message', "Task {$id} removed");
})->where('id', '[0-9]+');

// 8. Route with multiple parameters - user tasks view
Route::get('/users/{userId}/tasks/{taskId}', function ($userId, $taskId) {
	return view('users.task', [
		'userId' => $userId,
		'taskId' => $taskId,
		'breadcrumb' => "User {$userId} > Task {$taskId}"
	]);
})->where(['userId' => '[0-9]+', 'taskId' => '[0-9]+']);

// 9. Route with query parameters - search results
Route::get('/search', function (\Illuminate\Http\Request $request) use ($tasks) {
	$query = $request->get('q', '');
	$completed = $request->get('completed');
	$limit = (int) $request->get('limit', 10);

	$filteredTasks = collect($tasks);

	if ($query) {
		$filteredTasks = $filteredTasks->filter(function ($task) use ($query) {
			return stripos($task->title, $query) !== false ||
				stripos($task->description, $query) !== false;
		});
	}

	if ($completed !== null) {
		$filteredTasks = $filteredTasks->filter(function ($task) use ($completed) {
			return $task->completed === filter_var($completed, FILTER_VALIDATE_BOOLEAN);
		});
	}

	return view('search.results', [
		'query' => $query,
		'results' => $filteredTasks->take($limit)->values(),
		'filters' => $request->all(),
		'total' => $filteredTasks->count()
	]);
});

// 10. Named route with dashboard view
Route::get('/dashboard', function () use ($tasks) {
	$completed = collect($tasks)->where('completed', true)->count();
	$pending = collect($tasks)->where('completed', false)->count();

	return view('dashboard', [
		'totalTasks' => count($tasks),
		'completedTasks' => $completed,
		'pendingTasks' => $pending,
		'tasks' => $tasks
	]);
})->name('dashboard');

// 11. Route group with prefix - admin panel
Route::prefix('admin')->group(function () use ($tasks) {
	Route::get('/stats', function () use ($tasks) {
		$completed = collect($tasks)->where('completed', true)->count();
		$pending = collect($tasks)->where('completed', false)->count();

		return view('admin.stats', [
			'totalTasks' => count($tasks),
			'completedTasks' => $completed,
			'pendingTasks' => $pending,
			'completionRate' => count($tasks) > 0 ? round(($completed / count($tasks)) * 100, 2) : 0
		]);
	});

	Route::get('/export', function () use ($tasks) {
		$csv = "ID,Title,Description,Completed,Created\n";
		foreach ($tasks as $task) {
			$csv .= "{$task->id},\"{$task->title}\",\"{$task->description}\"," .
				($task->completed ? 'Yes' : 'No') . ",{$task->created_at}\n";
		}

		return response($csv)
			->header('Content-Type', 'text/csv')
			->header('Content-Disposition', 'attachment; filename="tasks_export.csv"');
	});
});

// 12. Fallback route with custom 404 view
Route::fallback(function () {
	return response()->view('errors.404', [
		'message' => 'The page you are looking for does not exist.',
		'availableRoutes' => [
			'/' => 'Homepage',
			'/tasks' => 'View all tasks',
			'/tasks/{id}' => 'View specific task',
			'/tasks/{id}/details' => 'Task details',
			'/search' => 'Search tasks',
			'/dashboard' => 'Dashboard',
			'/admin/stats' => 'Admin statistics',
			'/admin/export' => 'Export tasks as CSV'
		]
	], 404);
});