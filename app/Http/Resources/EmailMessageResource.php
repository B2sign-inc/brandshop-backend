<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\Resource;

class EmailMessageResource extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'to_name' => $this->to_name,
            'to_address' => $this->to_address,
            'from_name' => $this->from_name,
            'from_address' => $this->from_address,
            'subject' => $this->subject,
            'body' => $this->body,
            'date_sent' => $this->date_sent,
        ];
    }
}
