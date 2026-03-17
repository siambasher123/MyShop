<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inquiry;

class ContactController extends Controller
{
    /**
     * Show the contact form.
     */
    public function showForm()
    {
        return view('contact');
    }

    /**
     * Handle form submission.
     */
    public function store(Request $request)
    {
        // ✅ Validate form inputs
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'email'           => 'required|email',
            'contact_number'  => 'required|digits_between:6,15',
            'message'         => 'required|string|max:2000',
        ]);

        // 💾 Store in database
        Inquiry::create($validated);

        // ✅ Redirect back to contact page (not home yet)
        return redirect()
            ->route('contact.form')
            ->with('success', 'Thank you, ' . $validated['first_name'] . '! Your message has been sent successfully.');
    }
}
