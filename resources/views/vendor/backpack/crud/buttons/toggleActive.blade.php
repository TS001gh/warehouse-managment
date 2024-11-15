{{-- ensures the button is only shown if the user has update permissions. --}}
@if ($crud->hasAccess('update', $entry))
    <form action="{{ url($crud->route . '/toggle-active') }}" method="POST" style="display:inline;">
        @csrf
        <input type="hidden" name="id" value="{{ $entry->getKey() }}">
        <button type="submit" class="btn btn-xs {{ $entry->is_active ? 'btn-success' : 'btn-danger' }}">
            <i class="{{ $entry->is_active ? 'la la-toggle-on' : 'la la-toggle-off' }}"></i>
        </button>
    </form>
@endif
