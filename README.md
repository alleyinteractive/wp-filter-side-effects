# Filter Side Effects

`add_filter_side_effect()` allows attaching a callback function to a WordPress filter without requiring that the callback function return the filtered value, use the filtered value, or even return any value. It wraps `add_filter()` and accepts the same arguments with the same defaults.

Filter side effects thus behave like `add_action()` callbacks and can be used in situations where a particular call to `apply_filters()` signals that some behavior needs to occur but no convenient action exists to run it.

For example:

```php
<?php

use function Alley\WP\add_filter_side_effect;

add_filter_side_effect(
    'rest_pre_insert_post',
    function ( $prepared_post, $request ) {
        // Do something before the post is saved, like...
        $language_slug    = $request['lang'];
        $default_category = $this->get_custom_default_language_category( $language_slug );

        if ( $default_category ) {
            add_filter( 'pre_option_default_category', fn() => $default_category );
        }
    },
    10,
    2,
);
```
