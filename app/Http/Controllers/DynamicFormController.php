<?php

namespace App\Http\Controllers;

use App\Models\Form;
use App\Models\FormSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class DynamicFormController extends Controller
{
    public function list()
    {
        $forms = Form::where('is_active', true)->orderBy('name')->get();
        return view('forms.list', compact('forms'));
    }

    public function show($slug)
    {
        $form = Form::where('slug', $slug)->where('is_active', true)->firstOrFail();
        return view('forms.show', compact('form'));
    }

    public function submit(Request $request, $slug)
    {
        $form = Form::where('slug', $slug)->where('is_active', true)->firstOrFail();

        // Build rules dinamis
        $rules = [];
        foreach ($form->fields as $f) {
            $name = $f['name'];
            $type = $f['type'];
            $req  = !empty($f['required']) ? 'required' : 'nullable';

            switch ($type) {
                case 'number':
                    $rules[$name] = [$req,'numeric'];
                    if (isset($f['min'])) $rules[$name][] = 'min:'.$f['min'];
                    if (isset($f['max'])) $rules[$name][] = 'max:'.$f['max'];
                    break;
                case 'email':
                    $rules[$name] = [$req,'email','max:200'];
                    break;
                case 'tel':
                case 'text':
                case 'textarea':
                    $rules[$name] = [$req,'string','max:5000'];
                    break;
                case 'date':
                    $rules[$name] = [$req,'date'];
                    break;
                case 'select':
                case 'radio':
                    $options = $f['options'] ?? [];
                    $rules[$name] = [$req, Rule::in($options)];
                    break;
                case 'checkbox':
                    // checkbox banyak pilihan -> array of values in options
                    $options = $f['options'] ?? [];
                    $rules[$name] = [$req,'array'];
                    $rules[$name.'.*'] = [Rule::in($options)];
                    break;
                case 'file':
                    // boleh set mime/size di f['mimes'], f['max']
                    $m = isset($f['mimes']) ? 'mimes:'.implode(',', (array)$f['mimes']) : 'file';
                    $rules[$name] = array_filter([$req, $m, isset($f['max']) ? 'max:'.$f['max'] : null]);
                    break;
                default:
                    $rules[$name] = [$req];
            }
        }

        $validated = $request->validate($rules);

        // Upload file (jika ada)
        $filesSaved = [];
        foreach ($form->fields as $f) {
            if ($f['type'] === 'file') {
                $name = $f['name'];
                if ($request->hasFile($name)) {
                    $file = $request->file($name);
                    $filesSaved[$name] = [
                        'name' => $file->getClientOriginalName(),
                        'mime' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'data' => base64_encode(file_get_contents($file->getRealPath())),
                    ];
                }
            }
        }


        $submission = FormSubmission::create([
            'form_id'      => $form->_id,
            'data'         => $validated,
            'files'        => $filesSaved,
            'submitted_by' => Auth::id(),
        ]);

        return redirect()->route('forms.show', $form->slug)->with('status','Data terkirim. Terima kasih!');
    }
}
