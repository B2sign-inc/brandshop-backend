<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\EmailMessageResource;

use Auth;
use App\Models\EmailMessage;

class EmailMessagesController extends Controller
{
    use ApiResponse;

    public function index(Request $request)
    {
        $page = $request->get('page');
        $page = (intval($page) > 0) ? intval($page) : 1;

        $emailMessages = Auth::user()->emailMessages()->paginate(15, ['*'], 'page', $page);
        return EmailMessageResource::collection($emailMessages);
    }

    public function show(EmailMessage $emailMessage)
    {
        if ($emailMessage->user_id !== Auth::user()->id) {
            return $this->respondForbidden();
        }
        return new EmailMessageResource($emailMessage);
    }
}
