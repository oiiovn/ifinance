<form method="GET" class="d-flex align-items-center gap-2">
    <input type="date" name="from_date"
        class="form-control form-control-sm border-light rounded-3 shadow-sm text-transparent"
        style="max-width: 160px"
        value="{{ request('from_date', \Carbon\Carbon::now()->startOfMonth()->toDateString()) }}"
        onfocus="this.classList.remove('text-transparent')"
        onblur="if (!this.value) this.classList.add('text-transparent')">

    <input type="date" name="to_date"
        class="form-control form-control-sm border-light rounded-3 shadow-sm text-transparent"
        style="max-width: 160px"
        value="{{ request('to_date', \Carbon\Carbon::now()->endOfMonth()->toDateString()) }}"
        onfocus="this.classList.remove('text-transparent')"
        onblur="if (!this.value) this.classList.add('text-transparent')">

    <button type="submit" class="btn btn-info btn-load">
        <span class="d-flex align-items-center">
            <span class="flex-grow-1 me-2">
                Lọc
            </span>
            <span class="spinner-grow flex-shrink-0" role="status">
                <span class="visually-hidden">Lọc</span>
            </span>
        </span>
    </button>

</form>