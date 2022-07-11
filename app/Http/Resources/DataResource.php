<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class DataResource extends JsonResource
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
            'user_id' => $this->user_id,
            'avg_heart_rate' => intval($this->avg_heart_rate),
            'step_changes' => intval($this->step_changes),
            'step' => intval($this->step),
            'label' => $this->label,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
