<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;
use Illuminate\Http\Request;

class FormSubmissionPublicController extends Controller
{
    public function index()
    {
        $submissions = FormSubmission::with('form')
            ->orderBy('_id','desc')
            ->paginate(15);

        return view('form-submissions.index', compact('submissions'));
    }

    public function show(FormSubmission $submission)
    {
        return view('form-submissions.show', compact('submission'));
    }
}
