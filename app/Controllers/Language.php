<?php

namespace App\Controllers;

class Language extends BaseController
{
    public function switch(string $locale)
    {
        $supportedLocales = config('App')->supportedLocales;
        if (! in_array($locale, $supportedLocales, true)) {
            $locale = config('App')->defaultLocale;
        }

        session()->set('site_locale', $locale);

        $redirectUrl = previous_url() ?: site_url('/');
        return redirect()->to($redirectUrl);
    }
}
