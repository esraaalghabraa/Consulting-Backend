<?php


namespace App\Helpers;


trait Response
{
    public function handleResponse($data= null, $message = null, $status = null){
        $array = [
            'data'=>$data,
            'message'=>$message,
            'status'=>$status,
        ];
        return response($array,$status);
    }
}
