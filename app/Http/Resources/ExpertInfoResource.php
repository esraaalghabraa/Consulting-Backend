<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ExpertInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            "id"=> $this->resource->id,
            "name"=> $this->resource->name,
            "phone_number"=> $this->resource->phone_number,
            "remember_token"=> $this->resource->remember_token,
            "address"=> $this->resource->address,
            "photo"=> $this->resource->photo,
            "money"=> $this->resource->money,
            "rating"=> $this->resource->rating,
            "rating_number"=> $this->resource->rating_number,
            "start_work"=> $this->resource->start_work,
            "end_work"=> $this->resource->end_work,
            "category"=>$this->resource->category,
            "experiences"=>$this->resource->experiences,
            "work_days"=>$this->resource->workDays,
        ];
    }
}
