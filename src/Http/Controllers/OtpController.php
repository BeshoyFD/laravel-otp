<?php

/*
 * @copyright 2018 Hilmi Erdem KEREN
 * @license MIT
 */

namespace Erdemkeren\Otp\Http\Controllers;

use Erdemkeren\Otp\Events\OtpValidateEvent;
use Erdemkeren\Otp\OtpFacade as Otp;
use Erdemkeren\Otp\TokenInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Contracts\Validation\Validator as ValidatorInterface;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator as ValidatorFacade;

/**
 * Class OtpController.
 */
class OtpController
{
    /**
     * * Show the form for the otp submission.
     *
     * @return RedirectResponse|View
     */
    public function create()
    {
        if (! $this->otpHasBeenRequested()) {
            return redirect('/');
        }
        return view('otp.create');
    }

    /**
     * Store the otp in cookies and redirect user
     * to their original path.
     *
     * @param Request $request
     *
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        if (! $this->otpHasBeenRequested()) {
            return redirect('/');
        }

        $validator = $this->getOtpSubmissionRequestValidator($request);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator);
        }

        if (! $token = $this->retrieveOtpTokenByPlainText(
            $request->user(),
            $request->input('otp')
        )) {
            $validator->getMessageBag()->add(
                'otp',
                trans('The otp is not valid.')
            );

            event(new OtpValidateEvent($request,$request->user(),"fail"));

            return redirect()->back()->withErrors($validator);
        }

        if ($token->expired()) {
            $validator->getMessageBag()->add(
                'password',
                trans('The otp is expired.')
            );

            event(new OtpValidateEvent($request,$request->user(),"fail"));

            return redirect()->back()->withErrors($validator);
        }

        session()->forget('otp_requested');

        event(new OtpValidateEvent($request,$request->user(),"success"));

        return redirect()
            ->to(session()->pull('otp_redirect_url'))
            ->withCookie(
                cookie()->make('otp_token', (string) $token, config('otp.token_expire'))
            );
    }

    /**
     * Validate the given otp submission request.
     *
     * @param Request $request
     *
     * @return ValidatorInterface
     */
    private function getOtpSubmissionRequestValidator(Request $request): ValidatorInterface
    {
        return ValidatorFacade::make($request->all(), [
            'otp' => 'required|string',
        ]);
    }

    /**
     * Retrieve a token by the given user and password.
     *
     * @param Authenticatable $user
     * @param string          $password
     *
     * @return mixed
     */
    private function retrieveOtpTokenByPlainText(Authenticatable $user, string $password): ?TokenInterface
    {
        return Otp::retrieveByPlainText($user, $password);
    }

    /**
     * Determine if an otp requested or not.
     *
     * @return mixed
     */
    private function otpHasBeenRequested()
    {
        return session('otp_requested', false);
    }
}
