# wp-ajax
WP Ajax with OOP

the `WpAjax` class can be extended, every method that is public and not magic will automatically be added to its own wp_admin hook.
So for example:

```php
// Method helloWorld will be added to hook: "wp_ajax_helloWorld" and optionally "wp_ajax_nopriv_helloWorld"
public function helloWord()
    {
        return wp_send_json("hello world");
    }
```

Nopriv is added by default but can be set false.
