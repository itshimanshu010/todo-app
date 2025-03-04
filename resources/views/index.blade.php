<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Todo List</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/x-icon" href="{{ asset('assets/to-do-list.png')}}">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background-color: rgb(240, 241, 242);
        }

        .card {
            box-shadow: rgba(31, 38, 46, 0.12) 0px 2px 8px, rgba(31, 38, 46, 0.08) 0px 4px 16px;
            background-color: rgb(255, 255, 255);
            border-radius: 1.25rem;
            margin-bottom: 10px;
        }

        .list-group {
            color: #212529;
            text-decoration: none;
            background-color: #fff;
            border: 1px solid rgba(0, 0, 0, .125);
        }

        .task-checkbox {
            margin-right: 10px;
        }

        .add-task-container {
            display: flex;
            align-items: center;
            padding: 10px;
            border-bottom: 1px solid #eee;
        }

        .task-counter {
            color: #888;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <div class="card">
            <div class="card-body">
                <h2>Todo List</h2>
                <div class="mb-2">
                    <div class="form-check">
                        <input type="checkbox" id="showAllTasks" class="form-check-input">
                        <label class="form-check-label" for="showAllTasks">Show All Tasks</label>
                    </div>
                </div>
                <div class="mb-3">
                    <form id="taskForm">
                        <div class="input-group add-task-container">
                            <span class="task-counter">
                                <i class="fas fa-tasks"></i>
                                <span id="taskCount">{{ count($tasks) }}</span>
                            </span>
                            <input type="text" id="taskInput" class="form-control" placeholder="Project # To Do">
                            <button type="submit" class="btn btn-success">Add</button>
                        </div>
                    </form>
                </div>

                <ul id="taskList" class="list-group">
                    @foreach($tasks as $task)
                    <li class="list-group-item d-flex justify-content-between align-items-center" data-id="{{ $task->id }}">
                        <div>
                            <input type="checkbox" class="form-check-input task-checkbox me-2" {{ $task->completed ? 'checked' : '' }}>
                            <span class="{{ $task->completed ? 'text-decoration-line-through' : '' }}">{{ $task->title }}</span>
                        </div>
                        <button class="btn btn-outline-dark delete-task"><i class="fa fa-trash"></i></button>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Show All Tasks checkbox change
        $('#showAllTasks').change(function() {
            updateTaskVisibility();
        });

        function updateTaskCount() {
            $('#taskCount').text($('#taskList li:visible').length);
        }

        function updateTaskVisibility() {
            let showAll = $('#showAllTasks').is(':checked');
            $('.list-group-item').each(function() {
                let isCompleted = $(this).find('.task-checkbox').is(':checked');
                if (showAll) {
                    $(this).removeClass('d-none').addClass('d-flex');
                } else {
                    if (isCompleted) {
                        $(this).removeClass('d-flex').addClass('d-none');
                    } else {
                        $(this).removeClass('d-none').addClass('d-flex');
                    }
                }
            });
            updateTaskCount();
        }

        // On page load Initially we hide completed tasks since showAllTasks is unchecked by default
        $(document).ready(function() {
            updateTaskVisibility();
        });

        $('#taskForm').submit(function(e) {
            e.preventDefault();
            let title = $('#taskInput').val().trim();

            if (!title) {
                alert('Please enter a task title!');
                return;
            }

            $.ajax({
                url: "{{ route('store') }}",
                type: "POST",
                data: {
                    title: title
                },
                success: function(response) {
                    let newTask = `
                        <li class="list-group-item d-flex justify-content-between align-items-center" data-id="${response.id}">
                            <div>
                                <input type="checkbox" class="form-check-input task-checkbox me-2">
                                <span>${response.title}</span>
                            </div>
                            <button class="btn btn-outline-dark delete-task">Delete</button>
                        </li>
                    `;
                    $('#taskList').append(newTask);
                    $('#taskInput').val('');
                    updateTaskVisibility();
                },
                error: function(response) {
                    if (response.status === 422) {
                        alert('This task already exists!');
                    } else {
                        alert('An error occurred. Please try again.');
                    }
                }
            });
        });

        // Task checkbox change
        $(document).on('change', '.task-checkbox', function() {
            let taskItem = $(this).closest('li');
            let taskId = taskItem.data('id');

            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'PUT',
                success: function(response) {
                    if (response.completed) {
                        taskItem.find('span').addClass('text-decoration-line-through');
                        if (!$('#showAllTasks').is(':checked')) {
                            taskItem.removeClass('d-flex').addClass('d-none');
                        }
                    } else {
                        taskItem.find('span').removeClass('text-decoration-line-through');
                        taskItem.removeClass('d-none').addClass('d-flex');
                    }
                    updateTaskCount();
                },
                error: function() {
                    alert('Failed to update task. Please try again.');
                    $(this).prop('checked', !$(this).prop('checked'));
                }
            });
        });

        // Delete task
        $(document).on('click', '.delete-task', function() {
            if (!confirm('Are you sure you want to delete this task?')) {
                return;
            }

            let taskItem = $(this).closest('li');
            let taskId = taskItem.data('id');

            $.ajax({
                url: `/tasks/${taskId}`,
                type: 'DELETE',
                success: function() {
                    taskItem.remove();
                    updateTaskCount();
                },
                error: function() {
                    alert('Failed to delete task. Please try again.');
                }
            });
        });
    </script>
</body>

</html>