<div class="btn-group btn-group-sm" role="group">
    <a href="{{ route('doctors.show', $doctor) }}" class="btn btn-info text-white" title="View">
        <i class="bi bi-eye"></i>
    </a>
    <a href="{{ route('doctors.edit', $doctor) }}" class="btn btn-warning text-white" title="Edit">
        <i class="bi bi-pencil"></i>
    </a>
    <form action="{{ route('doctors.destroy', $doctor) }}" method="POST" class="d-inline"
          onsubmit="return confirm('Delete this doctor?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" title="Delete">
            <i class="bi bi-trash"></i>
        </button>
    </form>
</div>