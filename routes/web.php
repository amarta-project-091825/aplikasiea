<?php
use App\Http\Controllers\Admin\FormController;
use App\Http\Controllers\DynamicFormController;
use App\Http\Controllers\FormSubmissionPublicController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\UserManagementController;

Route::get('/', function () {
   return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
// 1a. Tes murni teks (tanpa role)
Route::get('/__ping', fn() => 'pong');

Route::middleware(['auth','verified','role:admin','role:1'])
    ->prefix('admin')->name('admin.')->group(function () {
        Route::get('/__ping', fn() => 'admin pong');
        Route::resource('users', UserManagementController::class)->except(['show']);
        Route::resource('forms', FormController::class); // index, create, store, edit, update, destroy

});

Route::middleware(['auth','verified'])->group(function () {
    Route::get('/forms', [DynamicFormController::class, 'list'])->name('forms.list');
    Route::get('/forms/{slug}', [DynamicFormController::class, 'show'])->name('forms.show');
    Route::post('/forms/{slug}', [DynamicFormController::class, 'submit'])->name('forms.submit');
    Route::get('/form-submissions', [FormSubmissionPublicController::class, 'index'])->name('form-submissions.index');
    Route::get('/form-submissions/{submission}', [FormSubmissionPublicController::class, 'show'])->name('form-submissions.show');
});


require __DIR__.'/auth.php';
