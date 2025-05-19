<form action="{{ $action }}" method="POST">
    @csrf
    @if ($method === 'PUT')
        @method('PUT')
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="row-md-4">

                <div class="form-group">
                    <label for="outletname">Outlet Name</label>
                    <input type="text" class="form-control" id="outletname" name="name"
                        placeholder="Ex: Outlet 1, Outlet 2, etc" value="{{ old('name', $outlet->name ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label>Outlet Type</label><br />
                    <div class="d-flex">
                        <div class="form-check">
                            <input checked class="form-check-input" type="radio" name="type" id="radioTypePOS"
                                value="pos" {{ old('type', $outlet->type ?? '') == 'pos' ? 'checked' : '' }} />
                            <label class="form-check-label" for="radioTypePOS">
                                Point of Sales
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="type" id="radioTypeWarehouse"
                                value="warehouse"
                                {{ old('type', $outlet->type ?? '') == 'warehouse' ? 'checked' : '' }} />
                            <label class="form-check-label" for="radioTypeWarehouse">
                                Warehouse
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label>Outlet Status</label><br />
                    <div class="d-flex">
                        <div class="form-check">
                            <input checked class="form-check-input" type="radio" name="status" id="radioStatusOpen"
                                value="open"
                                {{ old('status', $outlet->status ?? '') == 'open' ? 'checked' : '' }} />
                            <label class="form-check-label" for="radioStatusOpen">
                                Open
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="radioStatusClosed"
                                value="closed"
                                {{ old('status', $outlet->status ?? '') == 'closed' ? 'checked' : '' }} />
                            <label class="form-check-label" for="radioStatusClosed">
                                Closed
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="outletphone">Phone</label>
                    <input type="text" class="form-control" id="outletphone" name="phone"
                        placeholder="Ex: 08123456789, 0322123456" value="{{ old('phone', $outlet->phone ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="outletwa">Whatsapp</label>
                    <input type="text" class="form-control" id="outletwa" name="whatsapp"
                        placeholder="Ex: 08123456789, 0322123456" value="{{ old('whatsapp', $outlet->whatsapp ?? '') }}"
                        required />
                </div>

                <div class="form-group">
                    <label for="outletemail">Email</label>
                    <input type="email" class="form-control" id="outletemail" name="email"
                        placeholder="Ex: outlet@mail.com" value="{{ old('email', $outlet->email ?? '') }}" required />
                </div>

                <div class="form-group">
                    <label for="outletaddress">Address</label>
                    <input type="text" class="form-control" id="outletaddress" name="address"
                        placeholder="Ex: Jl. Ir. H. Soekarno" value="{{ old('address', $outlet->address ?? '') }}"
                        required />
                </div>

            </div>
        </div>

        <x-action-buttons cancelRoute="{{ $cancelRoute }}" submitRoute="{{ $action }}" />
    </div>
</form>
