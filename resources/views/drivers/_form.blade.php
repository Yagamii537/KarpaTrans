<div class="row">

    <div class="col-12">
        <h5 class="fw-semibold mb-3">
            Información personal
        </h5>
    </div>

    <div class="col-md-4 mb-3">
        <label for="first_names" class="form-label">
            Nombres <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="first_names"
               id="first_names"
               class="form-control @error('first_names') is-invalid @enderror"
               value="{{ old('first_names', $driver->first_names ?? '') }}"
               required>

        @error('first_names')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="last_names" class="form-label">
            Apellidos <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="last_names"
               id="last_names"
               class="form-control @error('last_names') is-invalid @enderror"
               value="{{ old('last_names', $driver->last_names ?? '') }}"
               required>

        @error('last_names')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="identification" class="form-label">
            Cédula <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="identification"
               id="identification"
               maxlength="20"
               class="form-control @error('identification') is-invalid @enderror"
               value="{{ old('identification', $driver->identification ?? '') }}"
               required>

        @error('identification')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="birth_date" class="form-label">
            Fecha de nacimiento
        </label>

        <input type="date"
               name="birth_date"
               id="birth_date"
               class="form-control @error('birth_date') is-invalid @enderror"
               value="{{ old(
                   'birth_date',
                   isset($driver?->birth_date)
                       ? $driver->birth_date->format('Y-m-d')
                       : ''
               ) }}">

        @error('birth_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="employee_code" class="form-label">
            Código interno
        </label>

        <input type="text"
               name="employee_code"
               id="employee_code"
               class="form-control @error('employee_code') is-invalid @enderror"
               value="{{ old('employee_code', $driver->employee_code ?? '') }}">

        @error('employee_code')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4 mb-3">
        <label for="hire_date" class="form-label">
            Fecha de ingreso
        </label>

        <input type="date"
               name="hire_date"
               id="hire_date"
               class="form-control"
               value="{{ old(
                   'hire_date',
                   isset($driver?->hire_date)
                       ? $driver->hire_date->format('Y-m-d')
                       : ''
               ) }}">
    </div>

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-3">
            Información de contacto
        </h5>
    </div>

    <div class="col-md-4 mb-3">
        <label for="phone" class="form-label">
            Teléfono principal
        </label>

        <input type="text"
               name="phone"
               id="phone"
               class="form-control"
               value="{{ old('phone', $driver->phone ?? '') }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="secondary_phone" class="form-label">
            Teléfono adicional
        </label>

        <input type="text"
               name="secondary_phone"
               id="secondary_phone"
               class="form-control"
               value="{{ old(
                   'secondary_phone',
                   $driver->secondary_phone ?? ''
               ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="email" class="form-label">
            Correo electrónico
        </label>

        <input type="email"
               name="email"
               id="email"
               class="form-control @error('email') is-invalid @enderror"
               value="{{ old('email', $driver->email ?? '') }}">

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12 mb-3">
        <label for="address" class="form-label">
            Dirección
        </label>

        <textarea name="address"
                  id="address"
                  rows="2"
                  class="form-control">{{ old('address', $driver->address ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-3">
            Información de licencia
        </h5>
    </div>

    <div class="col-md-3 mb-3">
        <label for="license_number" class="form-label">
            Número de licencia <span class="text-danger">*</span>
        </label>

        <input type="text"
               name="license_number"
               id="license_number"
               class="form-control @error('license_number') is-invalid @enderror"
               value="{{ old(
                   'license_number',
                   $driver->license_number ?? ''
               ) }}"
               required>

        @error('license_number')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="license_type" class="form-label">
            Tipo de licencia <span class="text-danger">*</span>
        </label>

        <select name="license_type"
                id="license_type"
                class="form-select @error('license_type') is-invalid @enderror"
                required>

            <option value="">
                Seleccione
            </option>

            @foreach (['A', 'A1', 'B', 'C', 'C1', 'D', 'D1', 'E', 'E1', 'F', 'G'] as $type)
                <option value="{{ $type }}"
                    @selected(
                        old(
                            'license_type',
                            $driver->license_type ?? ''
                        ) === $type
                    )>

                    Licencia {{ $type }}
                </option>
            @endforeach
        </select>

        @error('license_type')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="license_issue_date" class="form-label">
            Fecha de emisión
        </label>

        <input type="date"
               name="license_issue_date"
               id="license_issue_date"
               class="form-control"
               value="{{ old(
                   'license_issue_date',
                   isset($driver?->license_issue_date)
                       ? $driver->license_issue_date->format('Y-m-d')
                       : ''
               ) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="license_expiration_date" class="form-label">
            Fecha de vencimiento <span class="text-danger">*</span>
        </label>

        <input type="date"
               name="license_expiration_date"
               id="license_expiration_date"
               class="form-control @error('license_expiration_date') is-invalid @enderror"
               value="{{ old(
                   'license_expiration_date',
                   isset($driver?->license_expiration_date)
                       ? $driver->license_expiration_date->format('Y-m-d')
                       : ''
               ) }}"
               required>

        @error('license_expiration_date')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="license_points" class="form-label">
            Puntos disponibles
        </label>

        <input type="number"
               name="license_points"
               id="license_points"
               min="0"
               max="30"
               class="form-control"
               value="{{ old(
                   'license_points',
                   $driver->license_points ?? ''
               ) }}">
    </div>

    <div class="col-md-3 mb-3">
        <label for="photo" class="form-label">
            Fotografía
        </label>

        <input type="file"
               name="photo"
               id="photo"
               accept=".jpg,.jpeg,.png,.webp"
               class="form-control @error('photo') is-invalid @enderror">

        @error('photo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3 mb-3">
        <label for="identification_document" class="form-label">
            Documento de cédula
        </label>

        <input type="file"
               name="identification_document"
               id="identification_document"
               accept=".pdf,.jpg,.jpeg,.png"
               class="form-control">
    </div>

    <div class="col-md-3 mb-3">
        <label for="license_document" class="form-label">
            Documento de licencia
        </label>

        <input type="file"
               name="license_document"
               id="license_document"
               accept=".pdf,.jpg,.jpeg,.png"
               class="form-control">
    </div>

    @isset($driver)
        @if (
            $driver->photo ||
            $driver->identification_document ||
            $driver->license_document
        )
            <div class="col-12 mb-3">
                <div class="alert alert-light border">
                    <div class="fw-semibold mb-2">
                        Archivos actuales
                    </div>

                    <div class="d-flex flex-wrap gap-2">

                        @if ($driver->photo)
                            <a href="{{ asset('storage/' . $driver->photo) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                Ver fotografía
                            </a>
                        @endif

                        @if ($driver->identification_document)
                            <a href="{{ asset(
                                'storage/' . $driver->identification_document
                            ) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                Ver cédula
                            </a>
                        @endif

                        @if ($driver->license_document)
                            <a href="{{ asset(
                                'storage/' . $driver->license_document
                            ) }}"
                               target="_blank"
                               class="btn btn-sm btn-outline-primary">
                                Ver licencia
                            </a>
                        @endif

                    </div>
                </div>
            </div>
        @endif
    @endisset

    <div class="col-12">
        <hr>

        <h5 class="fw-semibold mb-3">
            Contacto de emergencia
        </h5>
    </div>

    <div class="col-md-4 mb-3">
        <label for="emergency_contact_name" class="form-label">
            Nombre
        </label>

        <input type="text"
               name="emergency_contact_name"
               id="emergency_contact_name"
               class="form-control"
               value="{{ old(
                   'emergency_contact_name',
                   $driver->emergency_contact_name ?? ''
               ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="emergency_contact_phone" class="form-label">
            Teléfono
        </label>

        <input type="text"
               name="emergency_contact_phone"
               id="emergency_contact_phone"
               class="form-control"
               value="{{ old(
                   'emergency_contact_phone',
                   $driver->emergency_contact_phone ?? ''
               ) }}">
    </div>

    <div class="col-md-4 mb-3">
        <label for="emergency_contact_relationship" class="form-label">
            Parentesco
        </label>

        <input type="text"
               name="emergency_contact_relationship"
               id="emergency_contact_relationship"
               class="form-control"
               value="{{ old(
                   'emergency_contact_relationship',
                   $driver->emergency_contact_relationship ?? ''
               ) }}">
    </div>

    <div class="col-12 mb-3">
        <label for="notes" class="form-label">
            Observaciones
        </label>

        <textarea name="notes"
                  id="notes"
                  rows="3"
                  class="form-control">{{ old('notes', $driver->notes ?? '') }}</textarea>
    </div>

    <div class="col-12 mb-4">
        <div class="form-check form-switch">

            <input type="checkbox"
                   name="is_active"
                   id="is_active"
                   value="1"
                   class="form-check-input"
                   @checked(
                       old(
                           'is_active',
                           isset($driver) ? $driver->is_active : true
                       )
                   )>

            <label for="is_active" class="form-check-label">
                Conductor activo
            </label>
        </div>
    </div>

</div>

<div class="d-flex justify-content-end gap-2">

    <a href="{{ route('drivers.index') }}"
       class="btn btn-light">
        Cancelar
    </a>

    <button type="submit"
            class="btn btn-primary">

        <i class="ti ti-device-floppy me-1"></i>

        {{ isset($driver)
            ? 'Actualizar conductor'
            : 'Guardar conductor' }}
    </button>

</div>
