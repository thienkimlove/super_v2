<?php

namespace App\Http\Requests;

use App\User;
use Hash;
use Illuminate\Foundation\Http\FormRequest;

class UserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    protected function getValidatorInstance()
    {
        return parent::getValidatorInstance()->after(function ($validator) {
            // Call the after method of the FormRequest (see below)
            $this->after($validator);
        });
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'username' => 'required|max:255',
        ];

        if ($this->route('user')) {
            $optionalRules = [
                'email' => 'required|email|max:255|unique:users,email,' . $this->route('user'),
            ];
        } else {
            $optionalRules = [
                'email' => 'required|email|max:255|unique:users',
            ];
        }

        return array_merge($rules, $optionalRules);
    }

    public function after($validator)
    {
        if (! $this->filled('group_id')) {
            $validator->errors()->add('group_id.required', 'Vui lòng chọn group');
        }

        if (!$this->route('user') && (env('GOOGLE_AUTH_STOP') == 1) && ! $this->filled('password')) {
            $validator->errors()->add('password.required', 'Vui lòng không để trống password');
        }
    }

    public function messages()
    {
        return [
            'username.required' => 'Vui lòng không để trống tên người dùng',
            'email.required' => 'Vui lòng không để trống email',
            'group_id.required' => 'Vui lòng chọn group',
            'email.email' => 'Sai định dạng email',
        ];
    }

    public function store()
    {
        if (!$this->filled('status')) {
            $this->merge([
                'status' => 0,
            ]);
        }

        if (!$this->filled('group_id')) {
            $this->merge([
                'group_id' => 0,
            ]);
        }

        if (!$this->filled('permission_id')) {
            $this->merge([
                'permission_id' => 0,
            ]);
        }

        if (env('GOOGLE_AUTH_STOP') == 1) {
            $password = md5($this->get('password'));
        } else {
            $password = md5(time());
        }



        User::create(array_merge($this->all(), ['password' => $password]));


        return $this;
    }

    public function save($id)
    {
        $user = User::findOrFail($id);

        if (!$this->filled('status')) {
            $this->merge([
                'status' => 0,
            ]);
        }

        if (!$this->filled('group_id')) {
            $this->merge([
                'group_id' => 0,
            ]);
        }

        if (!$this->filled('permission_id')) {
            $this->merge([
                'permission_id' => 0,
            ]);
        }


        $data = $this->all();

        if ((env('GOOGLE_AUTH_STOP') == 1) && $this->filled('password') && $this->get('password')) {
            $data['password'] = md5($this->get('password'));
        }

        $user->update($data);


        return $this;
    }
}
