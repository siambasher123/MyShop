<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    /**
     * Display all contact inquiries.
     */
    public function index()
    {
        $inquiries = Inquiry::orderBy('created_at', 'desc')->get();
        return view('admin.inquiries', compact('inquiries'));
    }

    /**
     * Send a reply email to a specific inquiry.
     */
    public function reply(Request $request, $id)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $inquiry = Inquiry::findOrFail($id);

        try {
            // Send email through configured mailer (Gmail SMTP)
            Mail::raw($request->message, function ($mail) use ($inquiry, $request) {
                $mail->to($inquiry->email)
                    ->subject($request->subject)
                    ->from(config('mail.from.address'), config('mail.from.name'));
            });

            // Mark inquiry as replied
            $inquiry->update(['replied' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Reply sent successfully!'
            ]);

        } catch (\Exception $e) {
            // Handle any email errors gracefully
            return response()->json([
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ], 500);
        }
    }
}
