<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * B-01 FIX: Populate the FormRequest so it can be injected into uploadController()
 * as a replacement for the inline $request->validate([...]) block.
 *
 * authorize() was previously hardcoded to false (would block every request if used).
 * rules() was empty.
 */
class UploadDocumentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * The user must be authenticated and must have an office assigned.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        return $user !== null && $user->offices->isNotEmpty();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title'          => 'required|string|max:255',
            'description'    => 'required|string',
            'category'       => 'nullable|integer|exists:document_categories,id',
            'classification' => 'required|in:Public,Office Only,Custom Offices,Private',
            'from_office'    => 'required|exists:offices,id',
            'main_document'  => 'required|file|max:10240|mimes:pdf,doc,docx,jpg,jpeg,png',
            'attachments.*'  => 'file|mimes:jpeg,png,jpg,gif,pdf,docx|max:10240',
            'allowed_offices'=> 'required_if:classification,Custom Offices|array',
            'allowed_offices.*' => 'exists:offices,id',
            'archive'        => 'nullable|string',
            'forward'        => 'nullable|string',
        ];
    }

    /**
     * Custom error messages.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'allowed_offices.required_if' => 'Please select at least one office for Custom Offices classification.',
            'classification.in'           => 'The selected classification is invalid.',
        ];
    }
}
