{{-- resources/views/admin/dashboard.blade.php --}}
@extends('layouts.administrador.app')

@section('title', 'Dashboard')

@section('content')

    <div class="flex items-center justify-between space-y-2">
        <h2 class="text-3xl font-bold tracking-tight">Dashboard</h2>
    </div>
    <div dir="ltr" data-orientation="horizontal" class="space-y-4">
        <div role="tablist" aria-orientation="horizontal"
             class="inline-flex h-10 items-center justify-center rounded-md bg-muted p-1 text-muted-foreground"
             tabindex="0" data-orientation="horizontal" style="outline: none;">
            <button type="button" role="tab" aria-selected="true" aria-controls="radix-:r2:-content-overview"
                    data-state="active" id="radix-:r2:-trigger-overview"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm"
                    tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">Resumen
            </button>
            <button type="button" role="tab" aria-selected="false" aria-controls="radix-:r2:-content-analytics"
                    data-state="inactive" id="radix-:r2:-trigger-analytics"
                    class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm"
                    tabindex="-1" data-orientation="horizontal" data-radix-collection-item="">Analíticas
            </button>
        </div>
        <div data-state="active" data-orientation="horizontal" role="tabpanel"
             aria-labelledby="radix-:r2:-trigger-overview" id="radix-:r2:-content-overview" tabindex="0"
             class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 space-y-4"
             style="animation-duration: 0s;">
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-4">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Ingresos Totales</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             class="h-4 w-4 text-muted-foreground">
                            <path d="M12 2v20M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold">$45,231.89</div>
                        <p class="text-xs text-muted-foreground">+20.1% respecto al mes anterior</p>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Ventas</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             class="h-4 w-4 text-muted-foreground">
                            <rect width="20" height="14" x="2" y="5" rx="2"></rect>
                            <path d="M2 10h20"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold">+2350</div>
                        <p class="text-xs text-muted-foreground">+180.1% respecto al mes anterior</p>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Clientes Activos</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             class="h-4 w-4 text-muted-foreground">
                            <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path>
                            <circle cx="9" cy="7" r="4"></circle>
                            <path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold">+573</div>
                        <p class="text-xs text-muted-foreground">+201 desde la última semana</p>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                    <div class="p-6 flex flex-row items-center justify-between space-y-0 pb-2">
                        <h3 class="tracking-tight text-sm font-medium">Tasa de Conversión</h3>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                             stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                             class="h-4 w-4 text-muted-foreground">
                            <path d="M22 12h-4l-3 9L9 3l-3 9H2"></path>
                        </svg>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="text-2xl font-bold">3.24%</div>
                        <p class="text-xs text-muted-foreground">+0.1% respecto al mes anterior</p>
                    </div>
                </div>
            </div>
            <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-7">
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm col-span-4" data-v0-t="card">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h3 class="text-2xl font-semibold leading-none tracking-tight">Resumen</h3>
                    </div>
                    <div class="p-6 pt-0 pl-2">
                        <div class="recharts-responsive-container" style="width: 100%; height: 350px; min-width: 0px;">
                            <div class="recharts-wrapper"
                                 style="position: relative; cursor: default; width: 465px; height: 350px;">
                                <svg class="recharts-surface" width="465" height="350" viewBox="0 0 465 350"
                                     style="width: 100%; height: 100%;">
                                    <title></title>
                                    <desc></desc>
                                    <defs>
                                        <clipPath id="recharts1-clip">
                                            <rect x="65" y="5" height="310" width="395"></rect>
                                        </clipPath>
                                    </defs>
                                    <g class="recharts-layer recharts-cartesian-axis recharts-xAxis xAxis">
                                        <g class="recharts-cartesian-axis-ticks">
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="81.45833333333333" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="81.45833333333333" dy="0.71em">Ene</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="114.37499999999999" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="114.37499999999999" dy="0.71em">Feb</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="147.29166666666666" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="147.29166666666666" dy="0.71em">Mar</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="180.20833333333334" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="180.20833333333334" dy="0.71em">Abr</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="213.125" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="213.125" dy="0.71em">May</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="246.04166666666666" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="246.04166666666666" dy="0.71em">Jun</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="278.9583333333333" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="278.9583333333333" dy="0.71em">Jul</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="311.87499999999994" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="311.87499999999994" dy="0.71em">Ago</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="344.79166666666663" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="344.79166666666663" dy="0.71em">Sep</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="377.7083333333333" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="377.7083333333333" dy="0.71em">Oct</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="410.62499999999994" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="410.62499999999994" dy="0.71em">Nov</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="bottom" width="395" height="30" stroke="none"
                                                      font-size="12" x="443.54166666666663" y="323"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="middle" fill="#888888">
                                                    <tspan x="443.54166666666663" dy="0.71em">Dic</tspan>
                                                </text>
                                            </g>
                                        </g>
                                    </g>
                                    <g class="recharts-layer recharts-cartesian-axis recharts-yAxis yAxis">
                                        <g class="recharts-cartesian-axis-ticks">
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="left" width="60" height="310" stroke="none"
                                                      font-size="12" x="57" y="315"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="end" fill="#888888">
                                                    <tspan x="57" dy="0.355em">$0</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="left" width="60" height="310" stroke="none"
                                                      font-size="12" x="57" y="237.5"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="end" fill="#888888">
                                                    <tspan x="57" dy="0.355em">$1500</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="left" width="60" height="310" stroke="none"
                                                      font-size="12" x="57" y="160"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="end" fill="#888888">
                                                    <tspan x="57" dy="0.355em">$3000</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="left" width="60" height="310" stroke="none"
                                                      font-size="12" x="57" y="82.5"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="end" fill="#888888">
                                                    <tspan x="57" dy="0.355em">$4500</tspan>
                                                </text>
                                            </g>
                                            <g class="recharts-layer recharts-cartesian-axis-tick">
                                                <text orientation="left" width="60" height="310" stroke="none"
                                                      font-size="12" x="57" y="9"
                                                      class="recharts-text recharts-cartesian-axis-tick-value"
                                                      text-anchor="end" fill="#888888">
                                                    <tspan x="57" dy="0.355em">$6000</tspan>
                                                </text>
                                            </g>
                                        </g>
                                    </g>
                                    <g class="recharts-layer recharts-bar">
                                        <g class="recharts-layer recharts-bar-rectangles">
                                            <g class="recharts-layer">
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="68.29166666666667" y="160.93" width="26" height="154.07"
                                                          radius="4,4,0,0" fill="#adfa1d" name="Ene"
                                                          class="recharts-rectangle" d="M68.29166666666667,164.93A 4,4,0,0,1,72.29166666666667,160.93L 90.29166666666667,160.93A 4,4,0,0,1,
                                          94.29166666666667,164.93L 94.29166666666667,315L 68.29166666666667,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="101.20833333333333" y="214.30166666666668" width="26"
                                                          height="100.69833333333332" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Feb" class="recharts-rectangle" d="M101.20833333333333,218.30166666666668A 4,4,0,0,1,105.20833333333333,214.30166666666668L 123.20833333333333,214.30166666666668A 4,4,0,0,1,
                                          127.20833333333333,218.30166666666668L 127.20833333333333,315L 101.20833333333333,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="134.12499999999997" y="217.76333333333332" width="26"
                                                          height="97.23666666666668" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Mar" class="recharts-rectangle" d="M134.12499999999997,221.76333333333332A 4,4,0,0,1,138.12499999999997,217.76333333333332L 156.12499999999997,217.76333333333332A 4,4,0,0,1,
                                          160.12499999999997,221.76333333333332L 160.12499999999997,315L 134.12499999999997,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="167.04166666666666" y="46.230000000000004" width="26"
                                                          height="268.77" radius="4,4,0,0" fill="#adfa1d" name="Abr"
                                                          class="recharts-rectangle" d="M167.04166666666666,50.230000000000004A 4,4,0,0,1,171.04166666666666,46.230000000000004L 189.04166666666666,46.230000000000004A 4,4,0,0,1,
                                          193.04166666666666,50.230000000000004L 193.04166666666666,315L 167.04166666666666,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="199.95833333333331" y="101.66833333333332" width="26"
                                                          height="213.33166666666668" radius="4,4,0,0" fill="#adfa1d"
                                                          name="May" class="recharts-rectangle" d="M199.95833333333331,105.66833333333332A 4,4,0,0,1,203.95833333333331,101.66833333333332L 221.95833333333331,101.66833333333332A 4,4,0,0,1,
                                          225.95833333333331,105.66833333333332L 225.95833333333331,315L 199.95833333333331,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="232.87499999999997" y="158.86333333333334" width="26"
                                                          height="156.13666666666666" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Jun" class="recharts-rectangle" d="M232.87499999999997,162.86333333333334A 4,4,0,0,1,236.87499999999997,158.86333333333334L 254.875,158.86333333333334A 4,4,0,0,1,
                                          258.875,162.86333333333334L 258.875,315L 232.87499999999997,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="265.7916666666667" y="181.85500000000002" width="26"
                                                          height="133.14499999999998" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Jul" class="recharts-rectangle" d="M265.7916666666667,185.85500000000002A 4,4,0,0,1,269.7916666666667,181.85500000000002L 287.7916666666667,181.85500000000002A 4,4,0,0,1,
                                          291.7916666666667,185.85500000000002L 291.7916666666667,315L 265.7916666666667,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="298.7083333333333" y="201.695" width="26" height="113.305"
                                                          radius="4,4,0,0" fill="#adfa1d" name="Ago"
                                                          class="recharts-rectangle" d="M298.7083333333333,205.695A 4,4,0,0,1,302.7083333333333,201.695L 320.7083333333333,201.695A 4,4,0,0,1,
                                          324.7083333333333,205.695L 324.7083333333333,315L 298.7083333333333,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="331.625" y="257.90833333333336" width="26"
                                                          height="57.09166666666664" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Sep" class="recharts-rectangle" d="M331.625,261.90833333333336A 4,4,0,0,1,335.625,257.90833333333336L 353.625,257.90833333333336A 4,4,0,0,1,
                                          357.625,261.90833333333336L 357.625,315L 331.625,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="364.5416666666667" y="239.30833333333334" width="26"
                                                          height="75.69166666666666" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Oct" class="recharts-rectangle" d="M364.5416666666667,243.30833333333334A 4,4,0,0,1,368.5416666666667,239.30833333333334L 386.5416666666667,239.30833333333334A 4,4,0,0,1,
                                          390.5416666666667,243.30833333333334L 390.5416666666667,315L 364.5416666666667,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="397.4583333333333" y="35.01833333333333" width="26"
                                                          height="279.9816666666667" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Nov" class="recharts-rectangle" d="M397.4583333333333,39.01833333333333A 4,4,0,0,1,401.4583333333333,35.01833333333333L 419.4583333333333,35.01833333333333A 4,4,0,0,1,
                                          423.4583333333333,39.01833333333333L 423.4583333333333,315L 397.4583333333333,315Z"></path>
                                                </g>
                                                <g class="recharts-layer recharts-bar-rectangle">
                                                    <path x="430.375" y="216.88500000000002" width="26"
                                                          height="98.11499999999998" radius="4,4,0,0" fill="#adfa1d"
                                                          name="Dic" class="recharts-rectangle" d="M430.375,220.88500000000002A 4,4,0,0,1,434.375,216.88500000000002L 452.375,216.88500000000002A 4,4,0,0,1,
                                          456.375,220.88500000000002L 456.375,315L 430.375,315Z"></path>
                                                </g>
                                            </g>
                                        </g>
                                        <g class="recharts-layer"></g>
                                    </g>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="rounded-lg border bg-card text-card-foreground shadow-sm col-span-3" data-v0-t="card">
                    <div class="flex flex-col space-y-1.5 p-6">
                        <h3 class="text-2xl font-semibold leading-none tracking-tight">Ventas Recientes</h3>
                    </div>
                    <div class="p-6 pt-0">
                        <div class="space-y-8">
                            <div class="flex items-center">
                                <span class="relative flex shrink-0 overflow-hidden rounded-full h-9 w-9"><span
                                        class="flex h-full w-full items-center justify-center rounded-full bg-muted">OM</span></span>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">Olivia Martín</p>
                                    <p class="text-sm text-muted-foreground">olivia.martin@email.com</p>
                                </div>
                                <div class="ml-auto font-medium">+$1,999.00</div>
                            </div>
                            <div class="flex items-center">
                                <span
                                    class="relative shrink-0 overflow-hidden rounded-full flex h-9 w-9 items-center justify-center space-y-0 border"><span
                                        class="flex h-full w-full items-center justify-center rounded-full bg-muted">JL</span></span>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">Jackson Lee</p>
                                    <p class="text-sm text-muted-foreground">jackson.lee@email.com</p>
                                </div>
                                <div class="ml-auto font-medium">+$39.00</div>
                            </div>
                            <div class="flex items-center">
                                <span class="relative flex shrink-0 overflow-hidden rounded-full h-9 w-9"><span
                                        class="flex h-full w-full items-center justify-center rounded-full bg-muted">IN</span></span>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">Isabella Nguyen</p>
                                    <p class="text-sm text-muted-foreground">isabella.nguyen@email.com</p>
                                </div>
                                <div class="ml-auto font-medium">+$299.00</div>
                            </div>
                            <div class="flex items-center">
                                <span class="relative flex shrink-0 overflow-hidden rounded-full h-9 w-9"><span
                                        class="flex h-full w-full items-center justify-center rounded-full bg-muted">WK</span></span>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">William Kim</p>
                                    <p class="text-sm text-muted-foreground">will@email.com</p>
                                </div>
                                <div class="ml-auto font-medium">+$99.00</div>
                            </div>
                            <div class="flex items-center">
                                <span class="relative flex shrink-0 overflow-hidden rounded-full h-9 w-9"><span
                                        class="flex h-full w-full items-center justify-center rounded-full bg-muted">SD</span></span>
                                <div class="ml-4 space-y-1">
                                    <p class="text-sm font-medium leading-none">Sofia Davis</p>
                                    <p class="text-sm text-muted-foreground">sofia.davis@email.com</p>
                                </div>
                                <div class="ml-auto font-medium">+$39.00</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="rounded-lg border bg-card text-card-foreground shadow-sm" data-v0-t="card">
                <div class="flex flex-col space-y-1.5 p-6">
                    <h3 class="text-2xl font-semibold leading-none tracking-tight">Productos Más Vendidos</h3>
                </div>
                <div class="p-6 pt-0">
                    <div class="relative w-full overflow-auto">
                        <table class="w-full caption-bottom text-sm">
                            <thead class="[&amp;_tr]:border-b">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <th class="h-12 px-4 text-left align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0">
                                    Producto
                                </th>
                                <th class="h-12 px-4 align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0 text-right">
                                    Ventas
                                </th>
                                <th class="h-12 px-4 align-middle font-medium text-muted-foreground [&amp;:has([role=checkbox])]:pr-0 text-right">
                                    Ingresos
                                </th>
                            </tr>
                            </thead>
                            <tbody class="[&amp;_tr:last-child]:border-0">
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 font-medium">Camiseta
                                    Básica
                                </td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">1234</td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">$24.680,00&nbsp;€</td>
                            </tr>
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 font-medium">Jeans Slim
                                    Fit
                                </td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">987</td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">$49.350,00&nbsp;€</td>
                            </tr>
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 font-medium">Zapatillas
                                    Deportivas
                                </td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">876</td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">$61.320,00&nbsp;€</td>
                            </tr>
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 font-medium">Vestido de
                                    Noche
                                </td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">765</td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">$68.850,00&nbsp;€</td>
                            </tr>
                            <tr class="border-b transition-colors hover:bg-muted/50 data-[state=selected]:bg-muted">
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 font-medium">Chaqueta de
                                    Cuero
                                </td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">654</td>
                                <td class="p-4 align-middle [&amp;:has([role=checkbox])]:pr-0 text-right">$98.100,00&nbsp;€</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
