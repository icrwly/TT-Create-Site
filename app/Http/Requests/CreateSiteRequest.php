<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateSiteRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Adjust as needed
    }

    public function rules()
    {
        return [
            'site_label' => 'required',
            'cms' => 'required',
            'supporting_org' => 'required',
            'site_plan' => 'required',
            'tag' => 'required',
        ];
    }
}

