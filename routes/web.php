<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UI\UiController;

use App\Http\Controllers\DepartmentController;

use App\Http\Controllers\SubjectController;

use App\Http\Controllers\ClassroomController;

use App\Http\Controllers\SubjectDistributionController;

use App\Http\Controllers\MaterialDownloadController;

use App\Http\Controllers\CourseRegistrationController;

Route::prefix('registration')->group(function () {
    Route::get('courses', [CourseRegistrationController::class, 'index'])->name('registration.courses');
    Route::get('courses/print', [CourseRegistrationController::class, 'print'])->name('registration.courses.print');

    Route::get('download-materials', [MaterialDownloadController::class, 'index'])->name('materials.download');
    Route::get('download-materials/print', [MaterialDownloadController::class, 'print'])->name('materials.download.print');

    Route::view('enrollment-stop', 'registration.enrollment-stop')->name('registration.enrollment-stop');
    Route::view('attendance-form', 'registration.attendance-form')->name('registration.attendance-form');
    Route::view('student-certificate', 'registration.student-certificate')->name('registration.student-certificate');
    Route::view('bank-report', 'registration.bank-report')->name('registration.bank-report');
    Route::view('department-report', 'registration.department-report')->name('registration.department-report');
});

Route::prefix('graduates')->name('graduates.')->group(function () {
    Route::view('transcript', 'graduates.transcript')->name('transcript');
    Route::view('list', 'graduates.list')->name('list');
});

Route::prefix('data-management')->name('data.')->group(function () {
    Route::view('backup', 'data_management.backup')->name('backup');
    Route::view('restore', 'data_management.restore')->name('restore');
    Route::view('reset', 'data_management.reset')->name('reset');
    Route::view('change-password', 'data_management.change-password')->name('change-password');
    Route::view('users', 'data_management.users')->name('users');
    Route::view('institute-number', 'data_management.institute-number')->name('institute-number');
    Route::view('institute-info', 'data_management.institute-info')->name('institute-info');
});

Route::view('accreditation', 'accreditation.index')->name('accreditation.index');



// قسم الدراسة والامتحانات
Route::prefix('study-exams')->group(function () {
    Route::view('results', 'study_exams.results')->name('results.index');
    Route::view('deprived', 'study_exams.deprived')->name('deprived.index');
    Route::view('grades', 'study_exams.grades')->name('grades.index');
    Route::view('final-results', 'study_exams.final-results')->name('final-results.index');
    Route::view('analysis', 'study_exams.analysis')->name('analysis.index');
    Route::view('projects', 'study_exams.projects')->name('projects.index');
    Route::view('second-round', 'study_exams.second-round')->name('second-round.index');
    Route::view('deprived-list', 'study_exams.deprived-list')->name('deprived-list.index');
    Route::view('grade-sheet', 'study_exams.grade-sheet')->name('grade-sheet.index');
    Route::view('statistics', 'study_exams.statistics')->name('statistics.index');
    Route::view('warnings', 'study_exams.warnings')->name('warnings.index');
});


Route::resource('classrooms', ClassroomController::class);


Route::resource('subjects', SubjectController::class);


Route::resource('departments', DepartmentController::class);


Route::resource('subject-distributions', SubjectDistributionController::class);
Route::get('subject-distributions/print', [SubjectDistributionController::class, 'print'])->name('subject-distributions.print');


Route::get('/', [UiController::class, 'dashboard'])->name('dashboard');
Route::get('/students', [UiController::class, 'studentsIndex'])->name('students.index');
Route::get('/students/create', [UiController::class, 'studentsCreate'])->name('students.create');
Route::get('/students/{id}', [UiController::class, 'studentsShow'])->name('students.show');
Route::get('/students/all-records', [UiController::class, 'studentsAllRecords'])->name('students.allRecords');

