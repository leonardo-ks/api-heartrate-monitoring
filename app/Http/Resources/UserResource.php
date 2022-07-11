<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {

        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $this->profile,
            'dob' => $this->dob,
            'gender' => $this->gender,
            'created_at' => Carbon::parse($this->created_at)->format('d-m-Y H:m:s'),
            'updated_at' => Carbon::parse($this->updated_at)->format('d-m-Y H:m:s')
        ];
    }
}
