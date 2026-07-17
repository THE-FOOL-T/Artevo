<?php

namespace App\Http\Requests\Admin;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SaveUserRoleRequest extends FormRequest
{
    /**
     * Authorization is also enforced at the route level (AdminMiddleware)
     * and via UserPolicy::updateRole for the specific target user — this
     * FormRequest layer is the last line of defense before the update runs.
     */
    public function authorize(): bool
    {
        return $this->user()->can('updateRole', $this->route('user'));
    }

    public function rules(): array
    {
        return [
            'role' => ['required', 'string', Rule::in(User::ROLES)],
        ];
    }
}
