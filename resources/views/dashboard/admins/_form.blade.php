<div class="form-group">
    <x-form.input
        label="Name"
        class="form-control-lg"
        name="name"
        :value="old('name', $admin->name)"
    />
</div>

<div class="form-group">
    <x-form.input
        label="Email"
        type="email"
        name="email"
        :value="old('email', $admin->email)"
    />
</div>

<div class="form-group">
    <x-form.input
        label="Password"
        type="password"
        name="password"
        placeholder="{{ $admin->exists ? 'Leave blank to keep current password' : '' }}"
    />
</div>

<div class="form-group">
    <x-form.input
        label="Confirm Password"
        type="password"
        name="password_confirmation"
    />
</div>

<fieldset class="mb-3">
    <legend>{{ __('Roles') }}</legend>

    @foreach ($roles as $role)
        <div class="form-check">
            <input
                class="form-check-input"
                type="checkbox"
                name="roles[]"
                value="{{ $role->id }}"
                id="role_{{ $role->id }}"
                @checked(in_array($role->id, old('roles', $admin_roles ?? [])))
            >
            <label class="form-check-label" for="role_{{ $role->id }}">
                {{ $role->name }}
            </label>
        </div>
    @endforeach
</fieldset>

<div class="form-group mt-3">
    <button type="submit" class="btn btn-primary">{{ $button_label ?? ($admin->exists ? 'Update' : 'Create') }}</button>
</div>
