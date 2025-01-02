<!-- Botón de favoritos -->
<button
    id="favoriteButton"
    class="inline-flex items-center justify-center
         rounded-md text-sm font-medium ring-offset-background
         transition-colors focus-visible:outline-none
         focus-visible:ring-2 focus-visible:ring-ring
         focus-visible:ring-offset-2 disabled:pointer-events-none
         disabled:opacity-50 border border-input bg-background
         hover:bg-accent hover:text-accent-foreground h-10 w-10"
    aria-label="Añadir a favoritos"
    data-favorite="false"
>
    <svg
        xmlns="http://www.w3.org/2000/svg"
        width="24" height="24"
        viewBox="0 0 24 24"
        fill="none" stroke="currentColor"
        stroke-width="2" stroke-linecap="round"
        stroke-linejoin="round"
        class="lucide lucide-heart h-6 w-6"
        id="heartIcon"
    >
        <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5
             0 0 0 16.5 3c-1.76 0-3 .5-4.5
             2-1.5-1.5-2.74-2-4.5-2A5.5
             5.5 0 0 0 2 8.5c0 2.3 1.5
             4.05 3 5.5l7 7Z">
        </path>
    </svg>
</button>
<script>
    const favoriteButton = document.getElementById('favoriteButton')
    const heartIcon = document.getElementById('heartIcon')

    favoriteButton.addEventListener('click', () => {
        // Leemos el estado actual
        const isFavorite = favoriteButton.getAttribute('data-favorite') === 'true'

        // Cambiamos el estado
        const newState = !isFavorite
        favoriteButton.setAttribute('data-favorite', newState.toString())

        // Si es favorito, rellenamos el corazón; si no, lo dejamos vacío
        if (newState) {
            // “fill” lo hace un color de relleno (usa fill="currentColor")
            heartIcon.classList.add('fill-current')
            favoriteButton.setAttribute('aria-label', 'Quitar de favoritos')
        } else {
            heartIcon.classList.remove('fill-current')
            favoriteButton.setAttribute('aria-label', 'Añadir a favoritos')
        }
    })
</script>
