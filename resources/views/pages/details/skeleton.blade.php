<style>
    /* Animación de carga */
    @keyframes shimmer {
        0% { background-position: -200px 0; }
        100% { background-position: 200px 0; }
    }

    .skeleton {
        background: linear-gradient(90deg, #ececec 25%, #f5f5f5 50%, #ececec 75%);
        background-size: 200% 100%;
        animation: shimmer 1.5s infinite linear;
        border-radius: 8px;
    }

    /* Estructura */
    .container-shimmer {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        display: grid;
        gap: 20px;
    }

    .image-placeholder {
        width: 100%;
        aspect-ratio: 1;
    }

    .thumbnail-shimmers-shimmer {
        display: flex;
        gap: 10px;
    }

    .thumbnail-shimmer {
        width: 80px;
        height: 80px;
    }

    .title-shimmer {
        width: 80%;
        height: 30px;
    }

    .rating-shimmer {
        width: 50%;
        height: 20px;
    }

    .price-shimmer {
        width: 30%;
        height: 25px;
    }

    .description-shimmer {
        width: 100%;
        height: 50px;
    }

    .color-shimmer-options {
        display: flex;
        gap: 10px;
    }

    .color-shimmer {
        width: 40px;
        height: 40px;
        border-radius: 50%;
    }

    .button-shimmer {
        width: 100%;
        height: 40px;
        margin-top: 10px;
    }
</style>
<div class="container-shimmer">
    <!-- Imagen principal -->
    <div class="skeleton image-placeholder"></div>

    <!-- Miniaturas -->
    <div class="thumbnail-shimmers-shimmer">
        <div class="skeleton thumbnail-shimmer"></div>
        <div class="skeleton thumbnail-shimmer"></div>
        <div class="skeleton thumbnail-shimmer"></div>
    </div>

    <!-- Información del producto -->
    <div class="skeleton title-shimmer"></div>
    <div class="skeleton rating-shimmer"></div>
    <div class="skeleton price-shimmer"></div>
    <div class="skeleton description-shimmer"></div>

    <!-- color-shimmeres disponibles -->
    <div class="color-shimmer-options">
        <div class="skeleton color-shimmer"></div>
        <div class="skeleton color-shimmer"></div>
        <div class="skeleton color-shimmer"></div>
        <div class="skeleton color-shimmer"></div>
    </div>

    <!-- Botón de compra -->
    <div class="skeleton button-shimmer"></div>
</div>
