<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Http\Controllers\ProvinciaRESTController;
use App\Provincia;
use App\Places;
use Validator;
use DB;

class PlacesRESTController extends Controller
{
  

  static public function getAll(){

      return DB::table('places')
      ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
      ->join('partido', 'places.idPartido', '=', 'partido.id')
      ->join('pais', 'places.idPais', '=', 'pais.id')
      ->where('places.aprobado', '=', 1)
      ->select()
      ->get();

  }

  static public function getScalar($pid,$cid,$bid){

      return DB::table('places')
      ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
      ->join('partido', 'places.idPartido', '=', 'partido.id')
      ->join('pais', 'places.idPais', '=', 'pais.id')
      ->where('places.idPais',  $pid)
      ->where('places.idProvincia', $cid)
      ->where('places.idPartido', $bid)
      ->where('places.aprobado', '=', 1)
      ->select()
      ->get();

  }


  
  static public function showApproved($pid,$cid,$bid){

      return DB::table('places')
      ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
      ->join('partido', 'places.idPartido', '=', 'partido.id')
      ->join('pais', 'places.idPais', '=', 'pais.id')
      ->where('places.idPais',  $pid)
      ->where('places.idProvincia', $cid)
      ->where('places.idPartido', $bid)
      ->where('places.aprobado', '=', 1)
      ->select()
      ->get();


  }


  static public function getCitiRanking(){

      return 
              DB::table('places')
                     ->select(DB::raw('count(*) as lugares, nombre_pais, 
                        nombre_provincia, nombre_partido'))
                     ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
                     ->join('partido', 'places.idPartido', '=', 'partido.id')
                     ->join('pais', 'places.idPais', '=', 'pais.id')
                     ->orderBy('lugares', 'desc')
                     ->groupBy('idPartido')
                     ->get();


  }
    static public function getNonGeo(){

      return 
              DB::table('places')
                     ->select(DB::raw('count(*) as lugares, nombre_pais, 
                        nombre_provincia, nombre_partido'))
                     ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
                     ->join('partido', 'places.idPartido', '=', 'partido.id')
                     ->join('pais', 'places.idPais', '=', 'pais.id')
                     ->whereNull('latitude')
                     ->orderBy('lugares', 'desc')
                     ->groupBy('idPartido')
                     ->get();


  }
    static public function getBadGeo(){

      return 
              DB::table('places')
                     ->select(DB::raw('count(*) as lugares, nombre_pais, 
                        nombre_provincia, nombre_partido'))
                     ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
                     ->join('partido', 'places.idPartido', '=', 'partido.id')
                     ->join('pais', 'places.idPais', '=', 'pais.id')
                     ->where('confidence','=',0.5)
                     ->orderBy('lugares', 'desc')
                     ->groupBy('idPartido')
                     ->get();


  }
  
  

  static public function showDreprecated(){

    return DB::table('places')
      ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
      ->join('partido', 'places.idPartido', '=', 'partido.id')
      ->join('pais', 'places.idPais', '=', 'pais.id')
      ->where('places.aprobado', '=', -1)
      ->select()
      ->get();

    }
      static public function showPending(){

    return DB::table('places')
      ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
      ->join('partido', 'places.idPartido', '=', 'partido.id')
      ->join('pais', 'places.idPais', '=', 'pais.id')
      ->where('places.aprobado', '=', 0)
      ->select()
      ->get();

    }


  public function showPanel($id)
   {
     return DB::table('places')
      ->join('provincia', 'places.idProvincia', '=', 'provincia.id')
      ->join('partido', 'places.idPartido', '=', 'partido.id')
      ->join('pais', 'places.idPais', '=', 'pais.id')
      ->where('places.placeId', '=', $id)
      ->select()
      ->get();

   }

    public function block(Request $request, $id){

        $request_params = $request->all();

       $place = Places::find($id);

       $place->aprobado = -1;

       $place->updated_at = date("Y-m-d H:i:s");
       $place->save();

        return [];
   }

      public function approve(Request $request, $id){

        $request_params = $request->all();

       $place = Places::find($id);

       $place->aprobado = 1;

       $place->updated_at = date("Y-m-d H:i:s");
       $place->save();

        return [];
   }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return Response
     */
    public function update(Request $request, $id)
    {
      $request_params = $request->all();

      $rules = array(
          'establecimiento' => 'required|max:150|min:4',
          'nombre_partido' => 'required|max:50|min:4',
          'nombre_provincia' => 'required|max:50|min:4',
          'nombre_pais' => 'required|max:50|min:4',
      );

      $messages = array(
          'required'    => 'El :attribute es requerido.',
          'max'    => 'El :attribute debe poseer un maximo de :max caracteres.',
        'min'    => 'El :attribute debe poseer un minimo de :min caracteres.');

      $validator = Validator::make($request_params,$rules,$messages);

      if ($validator->passes ()){
        $place = Places::find($id);


        $place->establecimiento = $request_params['establecimiento'];
        $place->calle = $request_params['calle'];
        $place->altura = $request_params['altura'];
        $place->cruce = $request_params['cruce'];
        $place->latitude = $request_params['latitude'];
        $place->longitude = $request_params['longitude'];


        $place->prueba = $request_params['prueba'];
        $place->responsable_testeo = $request_params['responsable_testeo'];
        $place->ubicacion_testeo = $request_params['ubicacion_testeo'];
        $place->horario_testeo = $request_params['horario_testeo'];
        $place->mail_testeo = $request_params['mail_testeo'];
        $place->tel_testeo = $request_params['tel_testeo'];
        $place->web_testeo = $request_params['web_testeo'];
        $place->observaciones_testeo = $request_params['observaciones_testeo'];



        $place->establecimiento = $request_params['establecimiento'];
        $place->calle = $request_params['calle'];
        $place->altura = $request_params['altura'];
        $place->cruce = $request_params['cruce'];
        $place->latitude = $request_params['latitude'];
        $place->longitude = $request_params['longitude'];


        // //Updating localidad
        // $localidad_tmp = LocalidadRESTController::showByNombre($request_params['nombre_localidad']);
        // if(is_null($localidad_tmp)){
        //     $localidad = new Localidad;
        //     $localidad->nombre_localidad = $request_params['nombre_localidad'];
        //     $localidad->idProvincia = $place->idProvincia;
        //     $localidad->updated_at = date("Y-m-d H:i:s");
        //     $localidad->created_at = date("Y-m-d H:i:s");
        //     $localidad->save();
        //     $place->idLocalidad = $localidad->id;
        // }else{
        //     $place->idLocalidad = $localidad_tmp->id;
        // }

        $place->updated_at = date("Y-m-d H:i:s");
        $place->save();
      }

      return $validator->messages();
    }



}
