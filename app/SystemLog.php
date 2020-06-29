<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;

class SystemLog extends Model
{
  public static function store(Request $request)
  {
      $model = new SystemLog;
      $model->url = $request->url;
      $model->http_code = $request->http_code;
      $model->payload = $request->payload ;
      $model->response = $request->response;
      $model->system = $request->system;
      $model->save();
  }
}
