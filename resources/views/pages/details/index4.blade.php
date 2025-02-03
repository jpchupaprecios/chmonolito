
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
        const csi = localStorage.getItem('client_session_id');
        const l = '/api/product/'+asin+'/amazon/direct/'+csi;
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
</script>
