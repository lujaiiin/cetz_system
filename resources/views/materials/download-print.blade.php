<!DOCTYPE html>
<html lang="ar">
<head>
<meta charset="UTF-8">
<title>طباعة المواد</title>
<style>
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #000; padding: 4px; }
</style>
</head>
<body>
<h1>تنزيل المواد</h1>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>اسم الطالب</th>
            <th>القسم</th>
            <th>المادة</th>
            <th>الفصل</th>
        </tr>
    </thead>
    <tbody>
        @foreach($materials as $material)
        <tr>
            <td>{{ $material['id'] }}</td>
            <td>{{ $material['student_name'] }}</td>
            <td>{{ $material['department'] }}</td>
            <td>{{ $material['subject'] }}</td>
            <td>{{ $material['semester'] }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<script>
    window.onload = function() { window.print(); }
</script>
</body>
</html>
