<x-admin-panel>

    <h1>Settings</h1>

    <form action="{{ route('settings.update') }}" method="POST">
        @csrf
        @foreach($settings as $setting)
            <div>
                <label>{{ $setting->key }}</label>
                <input type="text" name="value" value="{{ $setting->value }}">
            </div>
        @endforeach
        <button type="submit">Save Settings</button>
    </form>
</x-admin-panel>
