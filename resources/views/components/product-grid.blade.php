<div class="product-item">
    <form method="post" action="?action=add&product_id={{ $product_id }}">
        @csrf
        <div class="product-image"><img src="{{ $product_image }}"></div>
        <div class="favorite-switch" style="--star-color:{{ $product_fav ? 'red' : 'black' }};"
            onclick="document.location='?action=switch-favorite&product_id={{ $product_id }}'"></div>
        <div class="product-tile-footer">
            <div class="product-title">{{ $product_name }}</div>
            <div class="product-price">${{ $product_price }}</div>
            @auth
                <div class="cart-action"><input type="text" class="product-quantity" name="quantity" value="1"
                        size="2" /><input type="submit" value="Add to Cart" class="btnAddAction" />
                </div>
            @endauth
            <div class="product-rating">
                <div class="Stars" style="--rating: {{ $product_rating }};"></div>
            </div>
        </div>
    </form>
</div>
