

Checkout Session details
---

Provide all necessary config to create the Checkout Session
Ex:

```php
<?php

$details = [
     'customer_email' => 'mail@domain.tld',
     'line_items' => [
         [
             'amount' => 1000, // 10$
             'currency' => 'USD',
             'name' => 'My product',
             'quantity' => 1,
             'description' => 'My description',
             'images' => [
                 'https://myshop.tld/my_image_path.png'
             ],
         ]
     ],
     'payment_method_types' => ['card']
];

```

Decorates the [details provider service](./src/Provider/DetailsProvider.php) to add/remove/edit this array.
It will allow you to handle subscription or card setup mode for example.
