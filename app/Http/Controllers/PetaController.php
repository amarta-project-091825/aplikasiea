<?php

namespace App\Http\Controllers;

use App\Models\FormSubmission;

class PetaController extends Controller
{
    public function index($id = null)
    {
        $submission = null;
        $data = null;

        if ($id) {
            $submission = FormSubmission::find($id);

            if ($submission) {
                $data = is_string($submission->data)
                    ? json_decode($submission->data, true)
                    : $submission->data;
            }
        }

        return view('peta.index', compact('submission', 'data'));
    }
}
