<form wire:submit="save"
      class="border rounded shadow-lg">
    <div class="flex justify-between p-4 font-medium border-b">
        <span class="text-xl">{{ ucfirst($type) }} Detalles</span>
        @if ($type == 'shipping' && $editing)
            <label class="text-sm">
                <input type="checkbox"
                       value="1"
                       wire:model.live="shippingIsBilling" />
                Lo mismo que la facturación
            </label>
        @endif
    </div>
    <div class="p-4 space-y-4">
        @if ($editing)
            <div class="grid grid-cols-2 gap-4">
                <x-input.group label="Nombre(s)"
                               :errors="$errors->get('address.first_name')"
                               required>
                    <x-input.text wire:model.live="address.first_name"
                                  required />
                </x-input.group>

                <x-input.group label="Apellidos"
                               :errors="$errors->get('address.last_name')">
                    <x-input.text wire:model.live="address.last_name" />
                </x-input.group>
            </div>

            <div>
                <x-input.group label="Nombre de la compañía"
                               :errors="$errors->get('address.company_name')"
                               required>
                    <x-input.text wire:model.live="address.company_name"
                                  required />
                </x-input.group>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <x-input.group label="Teléfono de contacto"
                               :errors="$errors->get('address.contact_phone')">
                    <x-input.text wire:model.live="address.contact_phone" />
                </x-input.group>

                <x-input.group label="Correo electrónico de contacto"
                               :errors="$errors->get('address.contact_email')">
                    <x-input.text wire:model.live="address.contact_email"
                                  type="email" />
                </x-input.group>
            </div>

            <hr />

            <div class="grid grid-cols-3 gap-4">
                <x-input.group label="Dirección"
                               :errors="$errors->get('address.line_one')"
                               required>
                    <x-input.text wire:model.live="address.line_one"
                                  required />
                </x-input.group>
            </div>

            <div class="grid grid-cols-3 gap-4">
                <x-input.group label="Ciudad"
                               :errors="$errors->get('address.city')"
                               required>
                    <x-input.text wire:model.live="address.city"
                                  required />
                </x-input.group>

                <x-input.group label="Provincia"
                               :errors="$errors->get('address.state')">
                    <x-input.text wire:model.live="address.state" />
                </x-input.group>

                <x-input.group label="Código postal"
                               :errors="$errors->get('address.postcode')"
                               required>
                    <x-input.text wire:model.live="address.postcode"
                                  required />
                </x-input.group>
            </div>

            <div>
                <x-input.group label="País"
                               required>
                    <select class="w-full p-4 text-sm border-2 border-gray-200 rounded-lg"
                            wire:model.live="address.country_id">
                        <option value>Selecciona un país</option>
                        @foreach ($this->countries as $country)
                            <option value="{{ $country->id }}"
                                    wire:key="country_{{ $country->id }}">
                                {{ $country->native }}
                            </option>
                        @endforeach
                    </select>
                </x-input.group>
            </div>
        @else
            <dl class="flex">
                <div class="w-1/2">
                    <div class="space-y-4">
                        <div>
                            <dt class="text-sm font-medium">Nombre</dt>
                            <dd>{{ $address->first_name }} {{ $address->last_name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium">Compañía</dt>
                            <dd>{{ $address->company_name }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium">Número de teléfono</dt>
                            <dd>{{ $address->contact_phone }}</dd>
                        </div>

                        <div>
                            <dt class="text-sm font-medium">Correo electrónico</dt>
                            <dd>{{ $address->contact_email }}</dd>
                        </div>
                    </div>
                </div>

                <div class="w-1/2">
                    <dt class="text-sm font-medium">Dirección</dt>
                    <dd>
                        {{ $address->line_one }}<br>
                        @if ($address->city)
                            {{ $address->city }}<br>
                        @endif
                        {{ $address->state }}<br>
                        {{ $address->postcode }}<br>
                        {{ $address->country()->first()->native }}
                    </dd>
                </div>
            </dl>
        @endif
    </div>
    <div class="flex justify-end w-full p-4 bg-gray-100">
        <div>
            @if ($editing)
                <button type="submit"
                        wire:key="submit_btn"
                        class="px-5 py-3 font-medium text-white bg-blue-600 rounded-lg hover:bg-blue-500">
                    Continuar
                </button>
            @else
                <button type="button"
                        wire:key="edit_btn"
                        wire:click.prevent="$set('editing', true)"
                        class="px-5 py-3 font-medium bg-white border rounded-lg shadow-sm hover:bg-gray-50">
                    Editar Detalles
                </button>
            @endif
        </div>
    </div>
</form>
