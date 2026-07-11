<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * A single submission from the public Contact page.
 */
class ContactMessage extends Model
{
    use HasFactory;

    /**
     * Mass-assignable attributes. Deliberately explicit ($fillable, not
     * $guarded = []) so a future form change can never smuggle an
     * unexpected column (e.g. status) into a create() call.
     */
    protected $fillable = [
        'name',
        'email',
        'category',
        'subject',
        'message',
        'ip_address',
        'user_agent',
    ];

    /**
     * Available inquiry categories, shared between the Form Request
     * validation rule and the Blade select field.
     */
    public const CATEGORIES = [
        'general' => 'General Inquiry',
        'museum_inquiry' => 'Museum Partnership',
        'support' => 'Account Support',
        'bug_report' => 'Bug Report',
        'feature_suggestion' => 'Feature Suggestion',
    ];
}
