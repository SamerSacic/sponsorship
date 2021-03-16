<?php

namespace App\Http\Controllers;

use App\Exceptions\PaymentFailedException;
use App\Models\Sponsorable;
use App\Models\Sponsorship;
use App\Models\SponsorableSlot;
use Illuminate\Http\JsonResponse;

class SponsorableSponsorshipsController extends Controller
{
    private $paymentGateway;

    public function __construct($paymentGateway)
    {
        $this->paymentGateway = $paymentGateway;
    }

    public function new($slug)
    {
        $sponsorable = Sponsorable::findOrFailBySlug($slug);

        $sponsorableSlots = $sponsorable->slots()->sponsorable()->orderBy('publish_date')->get();

        return view('sponsorable-sponsorships.new', [
            'sponsorable' => $sponsorable,
            'sponsorableSlots' => $sponsorableSlots,
        ]);
    }

    public function store($slug): JsonResponse
    {
        try {
            request()->validate([
                'email' => 'required|email',
                'company_name' => 'required',
                'payment_token' => 'required',
                'sponsorable_slots' => ['required', 'array', function($attribute, $value, $fail) {
                    if (collect($value)->unique()->count() !== count($value)) {
                        $fail("You cannot sponsor the same slot more then once");
                    }
                }],
            ]);

            $sponsorable = Sponsorable::findOrFailBySlug($slug);

            $slots = $sponsorable->slots()->whereIn('id', request('sponsorable_slots'))->get();

            abort_unless($slots->count() === count(request('sponsorable_slots')), 400);

            $this->paymentGateway->charge(request('email'), $slots->sum('price'), request('payment_token'), "{$sponsorable->name} sponsorship");

            $sponsorship = Sponsorship::create([
                'email' => request('email'),
                'company_name' => request('company_name'),
                'amount' => $slots->sum('price')
            ]);

            $slots->each->update(['sponsorship_id' => $sponsorship->id]);

            return response()->json([], 201);

        } catch (PaymentFailedException $e) {
            return response()->json([], 422);
        }
    }
}
