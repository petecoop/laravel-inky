[![Actions Status](https://github.com/rsvpify/laravel-inky/workflows/Testing%20Laravel%20Package/badge.svg)](https://github.com/rsvpify/laravel-inky/actions)

Allows you to use Foundation's [Inky](http://foundation.zurb.com/emails/docs/inky.html) email templates nicely in Laravel 6-11.

Any views with a `.inky.php` extension will be compiled with both Inky and Blade, allowing you to use both templating engines seamlessly together. CSS is automatically inlined so styles work in email clients that don't support external stylesheets.

## Installation

Require with composer
```
composer require rsvpify/laravel-inky
```

## Usage

Check the [Foundation for Emails docs](http://foundation.zurb.com/emails/docs/index.html) for full usage on how to use Inky and Foundation for Emails CSS.

Use `Mail` as usual in Laravel

```php
Mail::send('emails.welcome', ['name' => $user->name], function ($m) use ($user) {
  $m->from('hello@app.com', 'Your Application');

  $m->to($user->email, $user->name)->subject('Welcome!');
});
```

You can create a Blade layout to inherit from e.g. `emails/layout.inky.php`

```blade
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width"/>
</head>
<body>
  @yield('content')
</body>
</html>
```

then create an Inky view e.g. `emails/welcome.inky.php`

```blade
@extends('emails.layout')

@section('content')
  <container>
    <row>
      <columns>Welcome, {{ $name }}</columns>
    </row>
  </container>
@stop
```

### CSS Inlining

Anything in your inky.php templates `<style>` elements is automatically inlined.

To apply CSS stylesheets to your inky.php templates, do not include any `<link>` elements.  Rather, run `php artisan vendor:publish` which will create a new `inky.php` file in your `config` directory.  This file contains an example stylesheet you will want to include for Foundation templates. Be sure to reference the location of the Foundation CSS file from your base path. You will have to obtain a recent copy of this file from Foundation, for instance at https://foundation.zurb.com/emails.html

```
'stylesheets' => [
        'public/css/foundation-emails.css',
        // you can add additional CSS files here to apply to your emails.
    ]
```

In the above array, reference any additional CSS file(s) you want to apply to your emails.  Be sure to include each file starting from the base path.

Here's a handy reference for CSS in emails: [CSS Support Guide for Email Clients](https://www.campaignmonitor.com/css/)

## Licence

[MIT Licence](LICENCE)
