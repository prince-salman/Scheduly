<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Task Report</title>
    <style>
        body { font-family: sans-serif; }
        table {
            width:100%;
            border-collapse: collapse;
        }
        th, td {
            border:1px solid #ddd;
            padding:8px;
        }
        th {
            background:#f3f3f3;
        }
    </style>
</head>
<body>

<h2>Task Report</h2>

<table>
    <thead>
        <tr>
            <th>Task</th>
            <th>Status</th>
            <th>Priority</th>
            <th>Deadline</th>
        </tr>
    </thead>
    <tbody>
        @foreach($tasks as $task)
        <tr>
            <td>{{ $task->title }}</td>
            <td>{{ $task->status }}</td>
            <td>{{ $task->priority }}</td>
            <td>{{ optional($task->due_date)->format('d M Y') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

</body>
</html>