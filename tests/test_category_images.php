<?php

declare(strict_types=1);

require_once dirname(__DIR__) . '/app/helpers/view.php';

$_SERVER['SCRIPT_NAME'] = '/Ecommerce-despensa/public/index.php';

function assert_category_image(bool $condition, string $message): void
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}

$fallback = '/Ecommerce-despensa/public/assets/images/categories/category-placeholder.svg';
assert_category_image(category_image(null) === $fallback, 'Empty categories must use the placeholder.');
assert_category_image(category_image('../../config/database.php') === $fallback, 'Manipulated paths must be rejected.');
assert_category_image(
    category_image('assets/images/categories/0123456789abcdef0123456789abcdef.webp')
        === '/Ecommerce-despensa/public/assets/images/categories/0123456789abcdef0123456789abcdef.webp',
    'Managed category image paths must be preserved.'
);

$advertisementFallback = '/Ecommerce-despensa/public/assets/images/advertising/advertising-placeholder.svg';
assert_category_image(advertisement_image(null) === $advertisementFallback, 'Empty advertisements must use the placeholder.');
assert_category_image(advertisement_image('../config/database.php') === $advertisementFallback, 'Manipulated advertisement paths must be rejected.');
assert_category_image(
    advertisement_image('assets/images/advertising/abcdefabcdefabcdefabcdefabcdefab.webp')
        === '/Ecommerce-despensa/public/assets/images/advertising/abcdefabcdefabcdefabcdefabcdefab.webp',
    'Managed advertisement paths must be preserved.'
);

$productFallback = '/Ecommerce-despensa/public/assets/images/product-placeholder.svg';
assert_category_image(product_image('../../config/database.php') === $productFallback, 'Manipulated product paths must be rejected.');
assert_category_image(
    product_image('assets/images/products/example.webp')
        === '/Ecommerce-despensa/public/assets/images/products/example.webp',
    'Managed product image paths must be preserved.'
);

echo "Category image tests passed.\n";
