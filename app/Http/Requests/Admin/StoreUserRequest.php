<?php
namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        // لو بس للادمن بدنا ياها
        // return auth()->check() && auth()->user()->role === 'admin';
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password'   => ['required', 'string', 'min:8'],
            'role'       => ['required', 'string', Rule::in(['user', 'admin'])],
        ];
    }
    public function messages(): array
    {
        return [
            'first_name.required' => 'الاسم الأول مطلوب.',
            'last_name.required'  => 'الاسم الأخير مطلوب.',
            'email.required'      => 'البريد الإلكتروني مطلوب.',
            'email.unique'        => 'هذا البريد مستخدم مسبقاً.',
            'password.required'   => 'كلمة المرور مطلوبة.',
            'password.min'        => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل.',
            'role.required'       => 'يجب اختيار دور المستخدم.',
            'role.in'             => 'قيمة الدور غير صالحة.',
        ];
    }
}
