@extends('layouts.app')

@section('content')
    <h1 class="text-3xl font-bold mb-8">Checkout</h1>
    <div class="flex flex-col lg:flex-row gap-8">
        <div class="lg:w-2/3">
            <form class="space-y-8">
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Información de envío</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="email">Correo electrónico</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="email" required="" type="email" value="" name="email"></div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="postalCode">Código postal</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="postalCode" required="" value="" name="postalCode"></div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="firstName">Nombre</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="firstName" required="" value="" name="firstName"></div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="lastName">Apellido</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="lastName" required="" value="" name="lastName"></div>
                        <div class="md:col-span-2"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="address">Dirección</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="address" required="" value="" name="address"></div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="exteriorNumber">Número Exterior</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="exteriorNumber" required="" value="" name="exteriorNumber"></div>
                        <div class="flex items-center space-x-2"><button type="button" role="checkbox" aria-checked="false" data-state="unchecked" value="on" class="peer h-4 w-4 shrink-0 rounded-sm border border-primary ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 data-[state=checked]:bg-primary data-[state=checked]:text-primary-foreground" id="noExteriorNumber"></button><input aria-hidden="true" tabindex="-1" type="checkbox" value="on" name="noExteriorNumber" style="transform: translateX(-100%); position: absolute; pointer-events: none; opacity: 0; margin: 0px; width: 16px; height: 16px;"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="noExteriorNumber">Sin número</label></div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="interiorNumber">Número Interior</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="interiorNumber" value="" name="interiorNumber"></div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="references">Referencias</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="references" value="" name="references"></div>
                        <div>
                            <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="province">Provincia del estado</label>
                            <button type="button" role="combobox" aria-controls="radix-:rk:" aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed" data-placeholder="" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;>span]:line-clamp-1">
                                <span style="pointer-events: none;">Selecciona una provincia</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 opacity-50" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            <select aria-hidden="true" tabindex="-1" style="position: absolute; border: 0px; width: 1px; height: 1px; padding: 0px; margin: -1px; overflow: hidden; clip: rect(0px, 0px, 0px, 0px); white-space: nowrap; overflow-wrap: normal;">
                                <option value="Ciudad de México">Ciudad de México</option>
                                <option value="Estado de México">Estado de México</option>
                                <option value="Jalisco">Jalisco</option>
                                <option value="Nuevo León">Nuevo León</option>
                            </select>
                        </div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="municipality">Municipio</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="municipality" required="" value="" name="municipality"></div>
                        <div>
                            <label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="colony">Colonia</label>
                            <button type="button" role="combobox" aria-controls="radix-:rl:" aria-expanded="false" aria-autocomplete="none" dir="ltr" data-state="closed" data-placeholder="" class="flex h-10 w-full items-center justify-between rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background placeholder:text-muted-foreground focus:outline-none focus:ring-2 focus:ring-ring focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50 [&amp;>span]:line-clamp-1">
                                <span style="pointer-events: none;">Selecciona una colonia</span>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-down h-4 w-4 opacity-50" aria-hidden="true">
                                    <path d="m6 9 6 6 6-6"></path>
                                </svg>
                            </button>
                            <select aria-hidden="true" tabindex="-1" style="position: absolute; border: 0px; width: 1px; height: 1px; padding: 0px; margin: -1px; overflow: hidden; clip: rect(0px, 0px, 0px, 0px); white-space: nowrap; overflow-wrap: normal;">
                                <option value="Centro">Centro</option>
                                <option value="Del Valle">Del Valle</option>
                                <option value="Polanco">Polanco</option>
                                <option value="Roma">Roma</option>
                            </select>
                        </div>
                        <div><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="phoneNumber">Número de Teléfono</label><input class="flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium file:text-foreground placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="phoneNumber" required="" type="tel" value="" name="phoneNumber"></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Método de envío</h2>
                    <div role="radiogroup" aria-required="false" dir="ltr" class="grid gap-2" tabindex="0" style="outline: none;">
                        <div class="flex items-center space-x-2"><button type="button" role="radio" aria-checked="false" data-state="unchecked" value="standard" class="aspect-square h-4 w-4 rounded-full border border-primary text-primary ring-offset-background focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="standard" tabindex="-1" data-radix-collection-item=""></button><input aria-hidden="true" tabindex="-1" type="radio" value="standard" style="transform: translateX(-100%); position: absolute; pointer-events: none; opacity: 0; margin: 0px; width: 16px; height: 16px;"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="standard">Envío estándar (3-5 días hábiles)</label></div>
                        <div class="flex items-center space-x-2"><button type="button" role="radio" aria-checked="false" data-state="unchecked" value="express" class="aspect-square h-4 w-4 rounded-full border border-primary text-primary ring-offset-background focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="express" tabindex="-1" data-radix-collection-item=""></button><input aria-hidden="true" tabindex="-1" type="radio" value="express" style="transform: translateX(-100%); position: absolute; pointer-events: none; opacity: 0; margin: 0px; width: 16px; height: 16px;"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="express">Envío express (1-2 días hábiles)</label></div>
                    </div>
                </div>
                <div class="bg-white p-6 rounded-lg shadow">
                    <h2 class="text-xl font-semibold mb-4">Método de pago</h2>
                    <div role="radiogroup" aria-required="false" dir="ltr" class="grid gap-2" tabindex="0" style="outline: none;">
                        <div class="flex items-center space-x-2"><button type="button" role="radio" aria-checked="false" data-state="unchecked" value="paypal" class="aspect-square h-4 w-4 rounded-full border border-primary text-primary ring-offset-background focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="paypal" tabindex="-1" data-radix-collection-item=""></button><input aria-hidden="true" tabindex="-1" type="radio" value="paypal" style="transform: translateX(-100%); position: absolute; pointer-events: none; opacity: 0; margin: 0px; width: 16px; height: 16px;"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="paypal">PayPal</label></div>
                        <div class="flex items-center space-x-2"><button type="button" role="radio" aria-checked="false" data-state="unchecked" value="creditCard" class="aspect-square h-4 w-4 rounded-full border border-primary text-primary ring-offset-background focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="creditCard" tabindex="-1" data-radix-collection-item=""></button><input aria-hidden="true" tabindex="-1" type="radio" value="creditCard" style="transform: translateX(-100%); position: absolute; pointer-events: none; opacity: 0; margin: 0px; width: 16px; height: 16px;"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="creditCard">Tarjeta de crédito</label></div>
                        <div class="flex items-center space-x-2"><button type="button" role="radio" aria-checked="false" data-state="unchecked" value="bankTransfer" class="aspect-square h-4 w-4 rounded-full border border-primary text-primary ring-offset-background focus:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50" id="bankTransfer" tabindex="-1" data-radix-collection-item=""></button><input aria-hidden="true" tabindex="-1" type="radio" value="bankTransfer" style="transform: translateX(-100%); position: absolute; pointer-events: none; opacity: 0; margin: 0px; width: 16px; height: 16px;"><label class="text-sm font-medium leading-none peer-disabled:cursor-not-allowed peer-disabled:opacity-70" for="bankTransfer">Transferencia bancaria</label></div>
                    </div>
                </div>
            </form>
        </div>
        <div class="lg:w-1/3">
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                <div class="p-6">
                    <h2 class="text-xl font-semibold mb-4">Resumen del pedido</h2>
                    <div class="space-y-4">
                        <div class="flex justify-between"><span>Camiseta Premium x2</span><span>$59.98</span></div>
                        <div class="flex justify-between"><span>Pantalón Vaquero x1</span><span>$49.99</span></div>
                        <div class="flex justify-between"><span>Zapatillas Deportivas x1</span><span>$79.99</span></div>
                        <div class="border-t pt-4">
                            <div class="flex justify-between"><span>Subtotal</span><span>$189.96</span></div>
                            <div class="flex justify-between"><span>Costo de envío</span><span>$9.99</span></div>
                            <div class="flex justify-between font-bold mt-4"><span>Total</span><span>$199.95</span></div>
                        </div>
                    </div>
                    <a href="/success">
                        <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-10 px-4 py-2 w-full mt-6" type="submit">Realizar Pedido</button>
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
