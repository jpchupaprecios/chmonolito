<link rel="stylesheet" href="/g/fancybox/jquery.fancybox.css">
<script src="/g/jq.js"></script>
<script src="/g/fancybox/jquery.fancybox.js"></script>
<!-- breadcrumb -->
<div class="grid md:grid-cols-2 gap-8">
    <!--<div>
        <div class="relative aspect-square mb-4">
            <img
                class="product-data-image rounded-lg"
                alt=""
                loading="lazy" decoding="async"
                data-nimg="fill"
                src="" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;">
        </div>
        <div class="flex space-x-2">
            <div class="relative w-20 h-20"><img alt="Camiseta Premium thumbnail 1" loading="lazy" decoding="async" data-nimg="fill" class="rounded-md" src="https://dummyimage.com/100x100/000/fff&text=Thumbnail%201" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"></div>
            <div class="relative w-20 h-20"><img alt="Camiseta Premium thumbnail 2" loading="lazy" decoding="async" data-nimg="fill" class="rounded-md" src="https://dummyimage.com/100x100/000/fff&text=Thumbnail%202" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"></div>
            <div class="relative w-20 h-20"><img alt="Camiseta Premium thumbnail 3" loading="lazy" decoding="async" data-nimg="fill" class="rounded-md" src="https://dummyimage.com/100x100/000/fff&text=Thumbnail%203" style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"></div>
        </div>
    </div>-->
    <div>
        <div id="wrapper-main-img" style="width: 550px; height: 550px; position: relative;">


            <div class="skeleton image-placeholder"></div>
            <img
                class="product-data-image rounded-lg"
                alt="Imagen principal"
                loading="lazy" decoding="async"
                data-nimg="fill"
                src=""
                style="display:none; position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"
            >
        </div>
        <!-- Thumbnails -->
        <div id="thumbnails-wrapper" class="relative w-20 h-20">

        </div>

    </div>
    <div>
        <div class="title-shimmer-wrapper">
            <div class="skeleton title-shimmer"></div>
            <br>
            <div class="skeleton title-shimmer"></div>
        </div>
        <h1 class="text-3xl font-bold mb-2 product-data-title"></h1>
        <div class="color-shimmer-options">
            <div class="skeleton color-shimmer"></div>
            <div class="skeleton color-shimmer"></div>
            <div class="skeleton color-shimmer"></div>
            <div class="skeleton color-shimmer"></div>
            <div class="skeleton color-shimmer"></div>
        </div>
        <div class="flex items-center mb-2 rating-stars-wrapper" style="display: none">
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
            </svg>
            <span class="ml-2 text-gray-600 product-data-rating"></span>
        </div>
        <div class="skeleton price-shimmer"></div>
        <p class="text-xl font-bold mb-4 product-data-price"></p>
        <p class="mb-4 product-data-description"></p>
        <div class="extra-options">

        </div>
        <div id="color-options">

        </div>
        <div id="selects">

        </div>
        <!-- quantity-controls -->
        <div class="flex items-center space-x-2">
            <button class="inline-flex items-center justify-center rounded-md text-sm font-medium ring-offset-background transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 bg-[#d94551] text-white hover:bg-[#b01721] h-10 px-4 py-2 flex-grow">Añadir al Carrito</button>
            <!-- fav -->
            </button>
        </div>
    </div>
</div>
<div id="extra-data-tabs"></div>
<!-- extra-data-tabs -->
<script>
    let product = null;
    let selectedVariants = [];
    let variants = [];
    let isLoading = false;
    let selectedStore = 'amazon'; // Asume un valor por defecto
    let combinations = [];
    let combinationSeparator = '';
    let selectedVariantAsin = '{{ //selectedVariantAsin}}';
    let isProductLoaded = false;



    const extraDataProduct = async (asin) => {
        const l = 'http://laravel11.local/api/product/'+asin+'/amazon/direct';
        const urls = [l];
        const requestOptions = {
            method: 'GET',
            headers: {
                "Content-Type": "application/json",
            }
        };
        const response = await Promise.race(urls.map(url => fetch(url, requestOptions)));
        const data = await response.json();

        if (data && data.status === 'ok') {
            const result = data.data;
            if (result && typeof result.extendedDetails !== "undefined") {
                let extendedDetails = result.extendedDetails
                if (extendedDetails) {
                    let extraOptions = document.getElementById('extra-data-tabs');
                    if (extraOptions) {
                        extraOptions.innerHTML = extendedDetails.html_images;
                    }
                }
            }
            if (result && typeof result.breadcrumbs_flat !== "undefined") {
                let bcs = result.breadcrumbs_flat.split(">");

                let html = `<li class="inline-flex items-center gap-1.5"><a class="transition-colors hover:text-foreground" href="/">Inicio</a></li>`;
                for(var i = 0; i < bcs.length; i++){
                    if(i > 5){
                        break;
                    }
                    let bc = bcs[i];
                    html += `<li aria-hidden="true" class="[&amp;>svg]:h-3.5 [&amp;>svg]:w-3.5" role="presentation">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right h-4 w-4">
                        <path d="m9 18 6-6-6-6"></path>
                </svg>
                </li>
                    <li class="inline-flex items-center gap-1.5"><a class="transition-colors hover:text-foreground" href="/categoria/electronica">` + bc + `</a></li>`;
                }

                document.querySelector('.flat-breadcrumbs').innerHTML = html;
            }
            if (result && typeof result.thumbnails !== "undefined") {
                document.getElementById("thumbnails-wrapper").innerHTML = "";
                let html = '<div class="gallery clearfix"> <div class="pics clearfix"> <div class="thumbs">';
                let first = null;
                for (var i=0; i < result.thumbnails.length; i++) {
                    let image = result.thumbnails[i];
                    if(!first){
                        first = image;
                    }
                    html += '<div class="preview"> <a href="#" data-full="' + image.link + '" data-title="Spring 2013 | Luna + Hill"> <img src="' + image.link + '"/> </a> </div>';
                }
                html += '</div> <a href="' + first.link + '" class="full" title="Spring 2013 | Luna + Hill"> <img src="' + first.link + '"> </a> </div>';
                let wrapperMainImg = document.getElementById('wrapper-main-img');

                if(typeof wrapperMainImg !== 'undefined' && wrapperMainImg !== null){
                    wrapperMainImg.style.display = 'none';

                    var container = document.querySelector('#thumbnails-wrapper');
                    if (container) {
                        container.insertAdjacentHTML('beforeend', html);
                    }

                    $(document).ready(function(){

                        $('.preview a').on('click', function(){
                            $('.selected').removeClass('selected');
                            $(this).addClass('selected');
                            var picture = $(this).data();

                            event.preventDefault(); //prevents page from reloading every time you click a thumbnail


                            $('.full img').fadeOut( 100, function() {
                                $('.full img').attr('src', picture.full);
                                $('.full').attr('href', picture.full);
                                $('.full').attr('title', picture.title);

                            }).fadeIn();
                        });// end on click

                        $('.full').fancybox({
                            helpers : {
                                title: {
                                    type: 'inside'
                                }
                            },
                            closeBtn : true,
                        });
                    });//end doc ready
                }
            }
        }
    }

    extraDataProduct(selectedVariantAsin);

    const handleVariantChange = (variantName, value, pType, pTarget) => {
        const type = pType ? pType : 'default';
        const target = pTarget ? pTarget : null;

        const updatedSelectedVariants = {
            ...selectedVariants,
            [variantName]: value
        };

        let variantSelectedValue = value;
        let variantSelected = null;

        if (combinations && combinations.length) {
            variantSelectedValue = null;
            let optionsSelected = [];

            var selects = document.getElementById("product-variants-wrapper").querySelectorAll(".variant-select");
            var imgs = document.getElementById("product-variants-wrapper").querySelectorAll(".variant-img-selected img");

            for (let i = 0; selects.length > i; i++) {
                let select = selects[i];
                let selectedOption = select.options[select.selectedIndex];
                let dataid = selectedOption.getAttribute("value");
                if (dataid) {
                    optionsSelected.push(dataid);
                }
            }

            for (let i = 0; imgs.length > i; i++) {
                let img = imgs[i];
                let dataid = img.getAttribute("dataid");
                if (dataid) {
                    if (img.getAttribute("group") == target.getAttribute("group")) {
                        if (target.getAttribute("dataid")) {
                            dataid = target.getAttribute("dataid");
                        }
                    }
                    optionsSelected.push(dataid);
                }
            }

            if (optionsSelected.length) {
                let allTrimCombinations = getAllTrimCombinations(optionsSelected, combinationSeparator);

                for (let i = 0; i < allTrimCombinations.length; i++) {
                    variantSelectedValue = getIdInCombinations(allTrimCombinations[i]);
                    if (variantSelectedValue) {
                        if (selectedStore == 'ebay') {
                            variantSelectedValue = asin + '|' + variantSelectedValue;
                        }
                        break;
                    }
                }
            }
        }

        if (variantSelectedValue) {
            setSelectedVariantAsin(variantSelectedValue);
            updateProduct(variantSelectedValue, variantSelected);
        }

        selectedVariants = updatedSelectedVariants;
    };

    /**
     * buildSelectVariantHTML
     *
     * Construye el HTML que antes generabas con tu PHP usando str_replace.
     * Recibe un objeto variant tal como lo tienes en JavaScript.
     */
    function buildSelectVariantHTML(variantData) {
        // 1. Obtenemos las opciones y el título
        let variantOptions = variantData.options || [];
        let title = variantData.title || '';

        // 2. Si no hay título, intentamos usar variantData.name
        if (!title) {
            title = variantData.name || '';
            if (title) {
                // Reemplazar guiones bajos por espacios y poner mayúscula inicial a cada palabra
                title = title
                    .replace(/_/g, ' ')
                    .replace(/\b\w/g, (l) => l.toUpperCase());

                // Ajustes de traducción (similar a tu if/elseif en PHP)
                if (title === 'Size Name') {
                    title = 'Tamaño';
                } else if (title === 'Color Name') {
                    title = 'Color';
                } else if (title === 'Service Provider') {
                    title = 'Proveedor';
                }

                // Quitar la palabra “Name” si está al final
                title = title.replace('Name', '');
            }
        }

        // 3. Plantilla base (como tu selects2.blade.php),
        //    usando placeholders <!-- select_name -->, <!-- options -->, <!-- lis -->
        //    para hacer los reemplazos después
        let template = `
    <div class="mb-6">
      <h3 class="font-semibold mb-2"><!-- select_name -->:</h3>
      <!-- CONTENEDOR DEL SELECT CUSTOM -->
      <div class="relative inline-block w-[180px]" id="sizeSelectContainer">

          <!-- BOTÓN que se ve siempre -->
          <button
              id="sizeSelectButton-`+variantData.name+`"
              type="button"
              class="flex h-10 w-full items-center justify-between
                     rounded-md border border-input bg-background px-3 py-2 text-sm
                     ring-offset-background focus:outline-none focus:ring-2
                     focus:ring-ring focus:ring-offset-2"
          >
              <span id="sizeSelectLabel-`+variantData.name+`">Seleccionar</span>
              <svg xmlns="http://www.w3.org/2000/svg"
                   width="24" height="24"
                   viewBox="0 0 24 24"
                   fill="none" stroke="currentColor"
                   stroke-width="2" stroke-linecap="round"
                   stroke-linejoin="round"
                   class="lucide lucide-chevron-down h-4 w-4 opacity-50"
              >
                  <path d="m6 9 6 6 6-6"></path>
              </svg>
          </button>

          <!-- LISTA DESPLEGABLE con las opciones -->
          <div
              id="sizeOptions-`+variantData.name+`"
              class="hidden absolute z-50 w-full bg-white border border-gray-200
                     rounded shadow-md mt-1"
          >
              <ul>
                  <!-- lis -->
              </ul>
          </div>

          <!-- SELECT REAL (OCULTO) PARA EL FORMULARIO -->
          <select
              id="hiddenSizeSelect-`+variantData.name+`"
              name="size"
              class="hidden"
          >
              <!-- options -->
          </select>
      </div>
    </div>
  `;

        // 4. Creamos el string de `<option>` y `<li>` igual que en tu PHP
        let selectOptions = '<option value="">Seleccionar</option>';
        let listItems = '';
        let selectedOption = null;
        // Recorremos cada option de la variante
        variantOptions.forEach((opt) => {
            const selected = opt.selected ? 'selected' : '';

            if(opt.selected){
                selectedOption = opt;
            }

            const available = !opt.available ? 'disabled' : '';
            selectOptions += `
      <option ${selected} ${available} value="${opt.sku}">
        ${opt.text}
      </option>
    `;
            listItems += `
      <li
        class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
        data-size="${opt.sku}"
      >
        ${opt.text}
      </li>
    `;
        });

        // 5. Reemplazamos en la plantilla:
        //    - <!-- select_name --> por el título
        //    - <!-- options --> por el string de <option>...
        //    - <!-- lis --> por el string de <li>...
        template = template.replace('<!-- select_name -->', title);
        template = template.replace('<!-- options -->', selectOptions);
        template = template.replace('<!-- lis -->', listItems);

        // 6. Devolvemos la plantilla resultante para ser inyectada en el DOM
        return [
            template,selectedOption
        ];
    }


    const updateProduct = async (asin, parentProductId) => {
        isLoading = true;
        isProductLoaded = false;
        //const apiUrl = `/rest/V1/chupaprecios/productdetail/?asin=${asin}&selected_store=${selectedStore}`;
        const apiUrl = `http://laravel11.local/api/product/${parentProductId}/amazon/direct`;

        const requestOptions = {
            method: 'GET',
            headers: {
                "Content-Type": "application/json",
            }
        };

        const urls = [apiUrl]; // Tres llamadas al mismo endpoint para tomar la más rápida
        //const urls = [apiUrl, apiUrl, apiUrl, apiUrl]; // Tres llamadas al mismo endpoint para tomar la más rápida
        try {
            toogleShimmers(true);
            document.getElementById('color-options').style.display = 'none';
            document.getElementById('selects').style.display = 'none';
            const response = await Promise.race(urls.map(url => fetch(url, requestOptions)));
            const data = await response.json();

            if (data && data.status === 'ok') {
                toogleShimmers(false);
                const result = data.data;
                if (result) {
                    if(typeof result.variants !== "undefined" && result.variants !== null) {


                        const colorSection = document.querySelector('#colorSection .flex.space-x-2');

// 1. Eliminamos los <img> actuales
                        if (colorSection) {
                            colorSection.innerHTML = '';
                        }

// 2. Iteramos las variantes
                        document.getElementById('selects').innerHTML = '';
                        for (var i = 0; i < result.variants.length; i++) {
                            let variant = result.variants[i];
                            let title = variant.title;

                            if (variant.type === 'image') {
                                document.getElementById('color-options').style.display = 'block';

                                // En este punto, si quieres, puedes borrar también lo que tenga colorOptions
                                // pero sobre todo elimina solo si es necesario
                                let colorOptions = document.getElementById('color-options');
                                colorOptions.innerHTML = '';
                                let imgHtml = "";
                                variantSelected = null;
                                for (var j = 0; j < variant.options.length; j++) {
                                    let option = variant.options[j];

                                    const selected = option.selected ? 'ring-2 ring-offset-2 ring-blue-500' : '';

                                    if(option.selected){
                                        variantSelected = option.sku;
                                    }

                                    let classSelected = selected ? 'selected-variant' : '';

                                    // Construir el HTML de la imagen
                                    imgHtml += `
        <img
          data-sku="${option.sku}"
          class="${classSelected} color-button w-16 border-2 focus:outline-none focus:ring-2 focus:ring-offset-2 border-gray-300 ${selected}"
          src="${option.img}"
        />
      `;

                                    // Agregamos este string al contenedor
                                }
                                if(imgHtml){
                                    imgHtml = `
                                        <div class="mb-4" id="colorSection">
                                            <h3 class="font-semibold mb-2">` + title + `:</h3>
                                            <div class="flex space-x-2">
                                                ` + imgHtml + `
                                            </div>
                                            <input type="hidden" id="colorInput" name="color" value="` + variantSelected + `">
                                        </div>
                                    `;

                                    document.getElementById('color-options').innerHTML = imgHtml;

                                    // Tomamos todos los botones de color
                                    const colorButtons = document.querySelectorAll(".color-button");
                                    // Tomamos el input oculto (si lo usamos)
                                    const hiddenColorInput = document.getElementById("colorInput");

                                    // Función que marca un botón como seleccionado
                                    function setSelectedColor(button) {
                                        // 1. Quitamos el “anillo” (ring) de todos los botones
                                        colorButtons.forEach((btn) => {
                                            btn.classList.remove("ring-2", "ring-offset-2", "ring-blue-500");
                                        });
                                        // 2. Agregamos el anillo al botón clicado
                                        button.classList.add("ring-2", "ring-offset-2", "ring-blue-500");

                                        // 3. Actualizamos el valor del input oculto
                                        if (hiddenColorInput) {
                                            hiddenColorInput.value = button.dataset.color;
                                        }

                                        const chosenAsin = button.getAttribute("data-sku")
                                        if (chosenAsin) {
                                            selectedVariantAsin = chosenAsin;
                                            updateProduct(chosenAsin, chosenAsin);
                                        }
                                    }

                                    // Asignamos el evento click a cada botón
                                    colorButtons.forEach((btn, index) => {
                                        btn.addEventListener("click", () => {
                                            setSelectedColor(btn);
                                        });
                                    });
                                }
                            } else {
                                // Generamos el HTML “selects2.blade.php” dinámicamente
                                let generatedHtml = buildSelectVariantHTML(variant);
                                let selectedOption = generatedHtml[1];
                                generatedHtml = generatedHtml[0];

                                // Inyectamos el HTML en el contenedor #selects (por ejemplo, concatenando)
                                const selectsContainer = document.getElementById('selects');
                                if (selectsContainer) {
                                    selectsContainer.innerHTML += generatedHtml;
                                    document.getElementById('selects').style.display = 'block';


                                    /**/
                                    eval(`sizeSelectButton${i} = document.getElementById("sizeSelectButton-"+variant.name)`);

                                    eval(`sizeSelectLabel${i} = document.getElementById("sizeSelectLabel-"+variant.name)`);

                                    eval(`sizeOptions${i} = document.getElementById("sizeOptions-"+variant.name)`);

                                    eval(`hiddenSizeSelect${i} = document.getElementById("hiddenSizeSelect-"+variant.name)`);


                                    eval(`
                                      sizeSelectButton${i}.addEventListener("click", () => {
                                        console.log("a");
                                        sizeOptions${i}.classList.toggle("hidden");
                                      });
                                    `);

                                    if (selectedOption) {
                                        eval(`
                                            sizeSelectLabel${i}.textContent = "${selectedOption["text"]}";
                                            sizeOptions${i}.addEventListener("click", (e) => {
                                              // Verificamos si se hizo click en un <li> con data-size
                                              if (e.target.matches("li[data-size]")) {
                                                const chosenValue = e.target.getAttribute("data-size");
                                                const chosenText = e.target.textContent;
                                                const chosenAsin = e.target.getAttribute("data-size");

                                                if (chosenAsin) {
                                                  selectedVariantAsin = chosenAsin;
                                                  updateProduct(chosenAsin, chosenAsin);
                                                }

                                                // Actualizamos el texto del label
                                                sizeSelectLabel${i}.textContent = chosenText;
                                                // Actualizamos el <select> oculto
                                                hiddenSizeSelect${i}.value = chosenValue;
                                                // Cerramos el dropdown
                                                sizeOptions${i}.classList.add("hidden");
                                              }
                                            });
                                          `);
                                                                            }

                                        eval(`
                                          document.addEventListener("click", (e) => {
                                            if (
                                              !sizeSelectButton${i}.contains(e.target) &&
                                              !sizeOptions${i}.contains(e.target)
                                            ) {
                                              sizeOptions${i}.classList.add("hidden");
                                            }
                                          });
                                        `);
                                    /**/

                                }
                            }
                        }


                    }
                    if(typeof result.image !== "undefined" && result.image !== null) {
                        const imageElement = document.querySelector('.product-data-image');
                        imageElement.src = result.image;
                    }
                    if(typeof result.price !== "undefined" && result.price !== null) {
                        const priceElement = document.querySelector('.product-data-price');
                        priceElement.textContent = "$ " + result.price + " MXN";
                    }
                    if(typeof result.rating !== "undefined" && result.rating !== null) {
                        const rating = parseFloat(result.rating);
                        let fullStars = Math.floor(rating);
                        const decimalPart = rating - fullStars;
                        let halfStar = 0;

                        if (decimalPart > 0) {
                            if (decimalPart >= 0.6) {
                                fullStars++;
                            } else {
                                halfStar = 1;
                            }
                        }

                        if (fullStars > 5) {
                            fullStars = 5;
                            halfStar = 0;
                        }

                        const emptyStars = 5 - (fullStars + halfStar);

                        let starsHtml = "";

                        // Generar estrellas llenas
                        for (let i = 0; i < fullStars; i++) {
                            starsHtml += `<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>`;
                        }

                        // Generar media estrella si es necesario
                        if (halfStar) {
                            starsHtml += `<svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                    <defs>
                        <linearGradient id="half-star">
                            <stop offset="50%" stop-color="#facc15"/>
                            <stop offset="50%" stop-color="#d1d5db"/>
                        </linearGradient>
                    </defs>
                    <path fill="url(#half-star)" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>`;
                        }

                        // Generar estrellas vacías
                        for (let i = 0; i < emptyStars; i++) {
                            starsHtml += `<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                </svg>`;
                        }

                        if(typeof result.score !== "undefined" && result.score !== null) {
                            // Agregar el texto del rating
                            starsHtml += `<span class="ml-2 text-gray-600 product-data-rating">${result.rating}</span>`;
                        }

                        // Seleccionar el contenedor de las estrellas
                        const ratingStarsWrapper = document.querySelector('.rating-stars-wrapper');
                        document.querySelector('.rating-stars-wrapper').style.display = 'flex';

                        // Actualizar el contenido del contenedor con el nuevo HTML
                        ratingStarsWrapper.innerHTML = starsHtml;
                        console.log("rating updated");
                    }

                    if(typeof result.title !== "undefined" && result.title !== null) {
                        const titleElement = document.querySelector('.product-data-title');
                        titleElement.textContent = result.title;
                    }

                    if (result && typeof result.thumbnails !== "undefined") {
                        document.getElementById("thumbnails-wrapper").innerHTML = "";
                        let html = '<div class="gallery clearfix"> <div class="pics clearfix"> <div class="thumbs">';
                        let first = null;
                        for (var i=0; i < result.thumbnails.length; i++) {
                            let image = result.thumbnails[i];
                            if(!first){
                                first = image;
                            }
                            html += '<div class="preview"> <a href="#" data-full="' + image.link + '" data-title="Spring 2013 | Luna + Hill"> <img src="' + image.link + '"/> </a> </div>';
                        }
                        html += '</div> <a href="' + first.link + '" class="full" title="Spring 2013 | Luna + Hill"> <img src="' + first.link + '"> </a> </div>';
                        let wrapperMainImg = document.getElementById('wrapper-main-img');

                        if(typeof wrapperMainImg !== 'undefined' && wrapperMainImg !== null){
                            wrapperMainImg.style.display = 'none';

                            var container = document.querySelector('#thumbnails-wrapper');
                            if (container) {
                                container.insertAdjacentHTML('beforeend', html);
                            }

                            $(document).ready(function(){

                                $('.preview a').on('click', function(){
                                    $('.selected').removeClass('selected');
                                    $(this).addClass('selected');
                                    var picture = $(this).data();

                                    event.preventDefault(); //prevents page from reloading every time you click a thumbnail


                                    $('.full img').fadeOut( 100, function() {
                                        $('.full img').attr('src', picture.full);
                                        $('.full').attr('href', picture.full);
                                        $('.full').attr('title', picture.title);

                                    }).fadeIn();
                                });// end on click

                                $('.full').fancybox({
                                    helpers : {
                                        title: {
                                            type: 'inside'
                                        }
                                    },
                                    closeBtn : true,
                                });
                            });//end doc ready
                        }
                    }

                    /*result.
                    result.
                    result.
                    result.*/
                    //result.has_combinations
                }
            } else {
                toogleShimmers(false);
                console.error('No data found for product.');
            }
        } catch (error) {
            toogleShimmers(false);
            console.error("Hubo un error buscando los productos:", error);
        } finally {
            toogleShimmers(false);
            isLoading = false;
            isProductLoaded = true;
        }
    };

    function toogleShimmers(show){
        if(show){
            document.querySelector('.product-data-price').style.display = 'none';
            document.querySelector('.price-shimmer').style.display = 'flex';

            document.querySelector('.product-data-image').style.display = 'none';
            document.querySelector('.image-placeholder').style.display = 'flex';


            document.querySelector('.product-data-title').style.display = 'none';
            document.querySelector('.title-shimmer-wrapper').style.display = 'block';



            document.querySelector('.color-shimmer-options').style.display = 'flex';
            document.querySelector('.rating-stars-wrapper').style.display = 'none';//flex
        }else{
            document.querySelector('.product-data-price').style.display = 'block';
            document.querySelector('.price-shimmer').style.display = 'none';

            document.querySelector('.product-data-image').style.display = 'block';
            document.querySelector('.image-placeholder').style.display = 'none';


            document.querySelector('.product-data-title').style.display = 'block';
            document.querySelector('.title-shimmer-wrapper').style.display = 'none';



            document.querySelector('.color-shimmer-options').style.display = 'none';
            document.querySelector('.rating-stars-wrapper').style.display = 'flex';//flex
        }
    }

    // Funciones auxiliares que necesitarás implementar o definir
    function getAllTrimCombinations(array, combinationSeparator) {
        function permute(arr) {
            if (arr.length === 0) return [[]];
            let result = [];

            for (let i = 0; i < arr.length; i++) {
                let rest = permute(arr.slice(0, i).concat(arr.slice(i + 1)));
                for (let permutation of rest) {
                    result.push([arr[i]].concat(permutation));
                }
            }

            return result;
        }

        let permutations = permute(array);

        let result = permutations.map(permutation => permutation.join(combinationSeparator));

        return result;
    }

    function getIdInCombinations(comb) {
        for(let i = 0; i < combinations.length; i++) {
            let combination = combinations[i];
            let variantKeys = combination.variant_key
            //let combinationsKeys = variantKeys.split(combinationSeparator);
            if(comb === variantKeys){
                return combination.variant_sku;
            }
        }

        return false;
    }

    function setProduct(product) {
        // Implementa la lógica para establecer el producto
    }

    function initializeSelectedVariants(variants) {
        // Implementa la lógica para inicializar las variantes seleccionadas
    }

    document.addEventListener('DOMContentLoaded', function() {
        // Selecciona la imagen principal
        const mainImage = document.querySelector('.product-data-image');

        // Selecciona todos los thumbs (cada <img> con clase .thumb-img)
        const thumbImages = document.querySelectorAll('.thumb-img');

        // Recorremos cada thumbnail y escuchamos el click
        thumbImages.forEach(thumb => {
            thumb.addEventListener('click', () => {
                // Cambiamos la imagen principal al src del thumb
                mainImage.src = thumb.src;

                // (Opcional) cambiar alt, o más datos
                mainImage.alt = thumb.alt;

                // (Opcional) si deseas indicar cuál está seleccionado,
                // podrías añadirle una clase .active al thumb seleccionado:
                thumbImages.forEach(img => img.classList.remove('ring-2', 'ring-offset-2', 'ring-blue-500'));
                thumb.classList.add('ring-2', 'ring-offset-2', 'ring-blue-500');
            });
        });
    });
</script>
