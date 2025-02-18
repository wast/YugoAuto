<?php

namespace App\Source\Public\App\Controllers;

use App\Source\Public\App\Requests\ContactRequest;
use App\Source\Public\Domain\Contact\ContactLogic;
use Illuminate\Support\Facades\Auth;

class PublicController
{
    public function sendMessage(
        ContactRequest $request,
        ContactLogic $logic
    ) {
        if (Auth::id()) {
            $logic->sendForAuthUser(Auth::id(), $request->message);
        } else {
            $logic->sendForGuest(
                $request->name,
                $request->email,
                $request->message
            );
        }

        $request->session()->flash('success', __('Message sent'));
        return redirect()->back();
    }

    public function contact()
    {
        return view('public.contact');
    }

    /**
     * @see https://lokalise.com/blog/laravel-localization-step-by-step/
     */
    public function changeLanguage(
        string $locale
    ) {
        app()->setLocale($locale);
        session()->put('locale', $locale);
        return redirect()->back();
    }

    public function androidStore()
    {
        return redirect(config('app.android_url'));
    }

    public function iphoneStore()
    {
        return redirect(config('app.ios_url'));
    }
}
