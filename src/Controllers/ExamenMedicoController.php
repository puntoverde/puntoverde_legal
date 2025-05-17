<?php

namespace App\Controllers;

use App\DAO\ExamenMedicoDAO;
use App\Mail\SolicitudMail;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;
use Illuminate\Support\Facades\Mail;

class ExamenMedicoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    public function allPersonas(Request $req)
    {
        $reglas = ["nombre" => "required"];
        $this->validate($req, $reglas);
        try {
            $data = ExamenMedicoDAO::allPersonas($req->input('nombre'));
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function findPersonaById($id)
    {
        try {
            $data = ExamenMedicoDAO::findPersonaById($id);
            return response()->json($data);
        } catch (\Exception $e) {
            dd($e);
        }

    }

    public function crearExamenMedico(Request $req)
    {
        $reglas = [
            "id_persona" => "required",
            "encargado" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::crearExamenMedico((object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function finalizarExamen(Request $req,$id){
        $reglas = [
            "observacion" => "required",
            "factores_riesgo" => "required",
            "resultado" => "required",
            "motivo" => "required_if:resultado,3",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::finalizarExamen($id,(object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getExamenMedico($id)
    {
        return response()->json(ExamenMedicoDAO::getExamenMedico($id),200,[],JSON_NUMERIC_CHECK);
    }

    public function getAntecedentesFamiliares($id)
    {
        return response()->json(ExamenMedicoDAO::getAntecedentesFamiliares($id),200,[],JSON_NUMERIC_CHECK);
    }

    public function saveAntecedentesFamiliares(Request $req)
    {
        $reglas = [
            "id_examen_medico"=>"required",
            "asma" => "required",
            "cancer" => "required",
            "cardiovascular" => "required",
            "diabetes" => "required",
            "neurologica" => "required",
            "presion" => "required",
            "reumatica" => "required",
            "tuberculosis" => "required"
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::saveAntecedentesFamiliares((object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function updateAntecedentesFamiliares(Request $req, $id)
    {
        $reglas = [
            "asma" => "required",
            "cancer" => "required",
            "cardiovascular" => "required",
            "diabetes" => "required",
            "neurologica" => "required",
            "presion" => "required",
            "reumatica" => "required",
            "tuberculosis" => "required"
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::updateAntecedentesFamiliares((object)$req->all(), $id);
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getHistoriaMedica($id)
    {
        return response()->json(ExamenMedicoDAO::getHistoriaMedica($id),200,[],JSON_NUMERIC_CHECK);
    }

    public function savetHistoriaMedica(Request $req)
    {
        $reglas = [
            "id_examen_medico"=>"required",
            "alergias" => "required",
            "asma" => "required",
            "cardiaca" => "required",
            "torax" => "required",
            "epilepsia" => "required",
            "diabetes" => "required",
            "cancer" => "required",
            "ulcera" => "required",
            "riñon" => "required",
            "perdida_peso" => "required",
            "visuales" => "required",
            "audicion" => "required",
            "reumaticos" => "required",
            "cirugias" => "required",
            "menstruales" => "required",
            "esguinses" => "required",
            "fuma" => "required",
            "alcohol" => "required",
            "edad_mestruacion" => "required",
            "numero_embarazo" => "required",
            "numero_partos" => "required",
            "cesareas" => "required",
            "numero_abortos" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::savetHistoriaMedica((object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function updatetHistoriaMedica(Request $req, $id)
    {
        $reglas = [
            "alergias" => "required",
            "asma" => "required",
            "cardiaca" => "required",
            "torax" => "required",
            "epilepsia" => "required",
            "diabetes" => "required",
            "cancer" => "required",
            "ulcera" => "required",
            "riñon" => "required",
            "perdida_peso" => "required",
            "visuales" => "required",
            "audicion" => "required",
            "reumaticos" => "required",
            "cirugias" => "required",
            "menstruales" => "required",
            "esguinses" => "required",
            "fuma" => "required",
            "alcohol" => "required",
            "edad_mestruacion" => "required",
            "numero_embarazo" => "required",
            "numero_partos" => "required",
            "cesareas" => "required",
            "numero_abortos" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::updatetHistoriaMedica((object)$req->all(), $id);
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function getAlimentacionHabitual($id)
    {
        return response()->json(ExamenMedicoDAO::getAlimentacionHabitual($id),200,[],JSON_NUMERIC_CHECK);
    }

    public function saveAlimentacionHabitual(Request $req)
    {
        $reglas = [
            "id_examen_medico"=>"required",
            "desayuno" => "required",
            "comida" => "required",
            "cena" => "required",
            "peso" => "required",
            "estatura" => "required",
            "fc" => "required",
            "pa" => "required",
            "edad_ejercicio" => "required",
            "deporte_inicia" => "required",
            "deporte_actual" => "required",
            "frecuencia" => "required",
            "intesidad" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::saveAlimentacionHabitual((object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function updateAlimentacionHabitual(Request $req, $id)
    {
        $reglas = [
            "id_examen_medico"=>"required",
            "desayuno" => "required",
            "comida" => "required",
            "cena" => "required",
            "peso" => "required",
            "estatura" => "required",
            "fc" => "required",
            "pa" => "required",
            "edad_ejercicio" => "required",
            "deporte_inicia" => "required",
            "deporte_actual" => "required",
            "frecuencia" => "required",
            "intesidad" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::updateAlimentacionHabitual((object)$req->all(), $id);
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }



    public function getExploracionFisica($id)
    {
        return response()->json(ExamenMedicoDAO::getExploracionFisica($id),200,[],JSON_NUMERIC_CHECK);
    }

    public function saveExploracionFisica(Request $req)
    {
        $reglas = [
            "id_examen_medico"=>"required",
            //cabeza
            "cabeza_ojos" => "required",
            "cabeza_nariz" => "required",
            "cabeza_faringe" => "required",
            "cabeza_boca" => "required",
            "cabeza_dentadura" => "required",
            "cabeza_audicion" => "required",
            "cabeza_vision" => "required",

            //cuello
            "cuello_forma" => "required",
            "cuello_volumen" => "required",
            "cuello_adenomegalia" => "required",
            "cuello_tiroides" => "required",
            "cuello_tumuraciones" => "required",
            "cuello_pulsos" => "required",

            //torax
            "torax_forma" => "required",
            "torax_volumen" => "required",
            "torax_ampliacion" => "required",
            "torax_amplexa" => "required",
            "torax_precusion" => "required",
            "torax_cardiacos" => "required",
            "torax_pulmunares" => "required",

            //abdomen
            "abdomen_forma" => "required",
            "abdomen_volumen" => "required",
            "abdomen_palpitacion" => "required",
            "abdomen_percusion" => "required",
            "abdomen_peristalsis" => "required",

            //toracicas
            "toracicas_forma" => "required",
            "toracicas_volumen" => "required",
            "toracicas_articular" => "required",
            "toracicas_pulsos" => "required",
            "toracicas_sensibilidad" => "required",
            "toracicas_osteotendino" => "required",

            //pelvis
            "pelvis_forma" => "required",
            "pelvis_volumen" => "required",
            "pelvis_articular" => "required",
            "pelvis_sensibilidad" => "required",
            "pelvis_osteotendino" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::saveExploracionFisica((object)$req->all());
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }

    public function updateExploracionFisica(Request $req, $id)
    {
        $reglas = [
            //cabeza
            "cabeza_ojos" => "required",
            "cabeza_nariz" => "required",
            "cabeza_faringe" => "required",
            "cabeza_boca" => "required",
            "cabeza_dentadura" => "required",
            "cabeza_audicion" => "required",
            "cabeza_vision" => "required",

            //cuello
            "cuello_forma" => "required",
            "cuello_volumen" => "required",
            "cuello_adenomegalia" => "required",
            "cuello_tiroides" => "required",
            "cuello_tumuraciones" => "required",
            "cuello_pulsos" => "required",

            //torax
            "torax_forma" => "required",
            "torax_volumen" => "required",
            "torax_ampliacion" => "required",
            "torax_amplexa" => "required",
            "torax_precusion" => "required",
            "torax_cardiacos" => "required",
            "torax_pulmunares" => "required",

            //abdomen
            "abdomen_forma" => "required",
            "abdomen_volumen" => "required",
            "abdomen_palpitacion" => "required",
            "abdomen_percusion" => "required",
            "abdomen_peristalsis" => "required",

            //toracicas
            "toracicas_forma" => "required",
            "toracicas_volumen" => "required",
            "toracicas_articular" => "required",
            "toracicas_pulsos" => "required",
            "toracicas_sensibilidad" => "required",
            "toracicas_osteotendino" => "required",

            //pelvis
            "pelvis_forma" => "required",
            "pelvis_volumen" => "required",
            "pelvis_articular" => "required",
            "pelvis_sensibilidad" => "required",
            "pelvis_osteotendino" => "required",
        ];

        $this->validate($req, $reglas);

        try {
            $data = ExamenMedicoDAO::updateExploracionFisica((object)$req->all(), $id);
            return $data;
        } catch (\Exception $e) {
            dd($e);
        }
    }
}
